<?php

require_once plugin_dir_path( __FILE__ ) . 'class-list-table.php';
require_once plugin_dir_path( __FILE__ ) . 'functions.php';
// require_once plugin_dir_path( __FILE__ ) . 'metabox-definitions.php';

function hdm_import_button_values() {
  if ( hdm_get_option('import_data_processed') == true)
  return array(
    'processed' => __('Processed', 'hoteldruid-migration'),
  );

  // rwmb_mete is not yet returning values here, using get_option instead
  $options = get_option('hoteldruid-migration', []);
  $backup_file = isset($_REQUEST['hoteldruid_backup_file']) ? $_REQUEST['hoteldruid_backup_file'] : $options['hoteldruid_backup_file'];
  $clients = get_transient('hoteldruid_migration_table_clients');
  $bookings = get_transient('hoteldruid_migration_table_bookings');
  $accommodations = get_transient('hoteldruid_migration_table_accommodations');

  if($clients && $bookings && $accommodations && !empty($backup_file)) return array(
    'ready' => 'Ready',
    'process' => __('Process', 'hoteldruid-migration'),
  );

  return array(
    '' => __('Not ready', 'hoteldruid-migration'),
  );
}

function import_data_field_validation($value = NULL, $request = NULL, $param = NULL) {
  $file =hdm_get_option('hoteldruid_backup_file', NULL);
  $clients = get_transient('hoteldruid_migration_table_clients');
  $bookings = get_transient('hoteldruid_migration_table_bookings');
  $accommodations = get_transient('hoteldruid_migration_table_accommodations');
  if (empty($clients) || empty($accommodations) || empty($bookings) || empty(hdm_get_option('hoteldruid_backup_file'))  )
  return '';
  if ( hdm_get_option('import_data_processed' ) == true) return  'processed';

  if($value == 'process') {
    error_log('Processing import file');
    // do some stuff and return 'processed' if succeeded, 'ready' if failed
  }
  return 'ready';
  // return $value;
}

function hdm_get_file_info($file) {
  $cache = wp_cache_get('hdm_backup_file_info', 'hoteldruid-migration');
  if(!empty($cache)) return $cache;
  if(empty($file) |! is_file($file) |! is_readable($file) ) {
    hdm_update_option('import_data', '');
    return false;
  }
  $info = pathinfo($file);
  $datetimeformat = 'l ' . get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) .':s';
  $info['mtime'] = wp_date( $datetimeformat, filemtime($file) );
  $info['mime'] =  mime_content_type($file);


  $tables = array();

  $clients = get_transient('hoteldruid_migration_table_clients');
  $bookings = get_transient('hoteldruid_migration_table_bookings');
  $accommodations = get_transient('hoteldruid_migration_table_accommodations');
  $years = get_transient('hoteldruid_migration_table_years');

  if ( $clients == false || $bookings == false || $accommodations == false ) {
    libxml_use_internal_errors(true);
    $dom = new DOMDocument;
    // $dom->loadHTMLFile($file);
    $str = file_get_contents($file);
    $dom->loadHTML(mb_convert_encoding($str, 'HTML-ENTITIES', 'UTF-8'));

    libxml_use_internal_errors(false);
    $years = array();
    $clients = array();
    $bookings = array();
    $accommodations = array();
    foreach( $dom->getElementsByTagName('tabella') as $node) {
      $tablename = $node->getElementsByTagName('nometabella')->item(0)->nodeValue;
      // error_log("tablename " . $tablename);

      switch(preg_replace('/[0-9]{4}$/', '', $tablename)) {
        case 'clienti':
        $items = flatten_array(node2array($node));
        $data = [];
        foreach ($items as $key => $item) {
          $phone = preg_replace('/[^0-9+]/', '', $item['telefono']);
          $item['country'] = $item['nazione'];
          if(preg_match('/^(0590|0690|\\+590|00590)/', $phone)) $item['country'] = "Guadeloupe";
          else if(preg_match('/^(0596|0696|\\+596|00596)/', $phone)) $item['country'] = "Martinique";
          else if(preg_match('/^(\\+|00)31/', $phone)) $item['country'] = "Netherlands";
          else if(preg_match('/^(\\+|00)32/', $phone)) $item['country'] = "Belgium";
          else if(preg_match('/^(\\+|00)33/', $phone)) $item['country'] = "France";
          else if(preg_match('/^(\\+|00)44/', $phone)) $item['country'] = "United Kingdom";
          else if(preg_match('/^(\\+|00)49/', $phone)) $item['country'] = "Germany";
          else if(preg_match('/^0[167][0-9]{8}/', $phone)) $item['country'] = "France";
          else if(!empty($item['country'])) $item['country'] = $item['country'];
          else if(!empty($item['nazionalita'])) $item['country'] = $item['nazionalita'];
          else if(!empty($item['nazionenascita'])) $item['country'] = $item['nazionenascita'];
          $item['phone'] = join(', ', array_filter([ $item['telefono'], $item['telefono2'], $item['telefono3'] ]));
          $emails = [ $item['email'], $item['email2'], $item['email3'] ];
          $emails = preg_replace('/.*@(guest.airbnb.com|guest.booking.com)/', '', $emails);
          $item['email'] = join(', ', array_filter( $emails ));
          unset($item['telefono'], $item['telefono2'], $item['telefono3']);
          $item['lastname'] = trim($item[ 'cognome' ]);
          $item['firstname'] = trim($item[ 'nome' ]);
          $item['displayname'] = trim($item[ 'firstname' ] . ' ' . $item[ 'lastname' ]);
          $item['street'] = preg_replace('/ Rue$/', '', trim($item[ 'numcivico' ] . ' ' . $item[ 'via' ]));

          $items[$key] = $item;
        }

        $clients = $items;
        break;

        case 'appartamenti':
        $accommodations = flatten_array(node2array($node));
        break;

        case 'prenota':
        $year=preg_replace('/.*([0-9]{4})$/', '$1', $tablename);
        $items = flatten_array(node2array($node));
        foreach ($items as $key => $item) {
          $item['idprenota'] = $year * 10000 + $item['idprenota'];
          $stamp_arrival = strtotime("$year-01-01 +" . $item['iddatainizio'] . " days -1 day");
          $stamp_departure = strtotime("$year-01-01 +" . $item['iddatafine'] . " days");
          $item['arrival'] = date('d-m-Y', $stamp_arrival);
          $item['departure'] = date('d-m-Y', $stamp_departure);
          $item['nights'] = ( $stamp_departure - $stamp_arrival ) / 86400;
          // $item['nights'] = NULL;

          if(empty($item['cat_persone']) || !preg_match('/(child|enfant)/', $item['cat_persone'])) {
            $item['adults'] = $item['num_persone'];
            $item['children'] = NULL;
          } else {
            $item['adults'] = preg_replace('/1([0-9]+)>s.*adult.*/', '$1', $item['cat_persone']);
            $item['children'] = preg_replace('/.*adult.*([0-9]+)>s>.*/', '$1', $item['cat_persone']);
          }

          $data[] = $item;
        }
        if(!empty($data)) {
          $bookings = array_merge($bookings, $data);
          $years[] = $year;
        }
        break;

        // default:
        // $tables[$tablename] = $data;
      }
    }

    set_transient('hoteldruid_migration_table_accommodations', $accommodations, 86400);
    set_transient('hoteldruid_migration_table_clients', $clients, 86400);
    set_transient('hoteldruid_migration_table_bookings', $bookings, 86400);
    set_transient('hoteldruid_migration_table_years', $years, 86400);

    if(!empty($accommodations) &! empty($clients) &! empty($bookings)) {
      hdm_update_option('import_data', 'ready');
    } else {
      hdm_update_option('import_data', '');
    }
  }

  $info['clients'] = count($clients);
  $info['bookings'] = count($bookings);
  $info['accommodations'] = count($accommodations);
  if(!empty($years)) $info['years'] = count($years) . " (from " . min($years) . " to " . max($years) . ")";

  wp_cache_set('hdm_backup_file_info', $info, 'hoteldruid-migration');
  // wp_cache_set('hdm_data_accommodations', $accommodations, 'hoteldruid-migration');
  // wp_cache_set('hdm_data_clients', $clients, 'hoteldruid-migration');
  // wp_cache_set('hdm_data_bookings', $bookings, 'hoteldruid-migration');
  // wp_cache_set('hdm_data_years', $years, 'hoteldruid-migration');
  return $info;
}

function backup_file_info_validation($value = NULL, $request = NULL, $param = NULL) {
  $file = hdm_get_option('hoteldruid_backup_file');
  if(empty($file)) {
    hdm_update_option('import_data', '');
  }
  return print_r(hdm_get_file_info($file), true);
}

function hoteldruid_backup_file_validation($value = NULL, $request = NULL, $param = NULL) {
  if(empty($value)) $value = NULL;
  $info = hdm_get_file_info($value);
  if($info != false && $info != null) {
    if($info['mime'] != 'text/x-php') {
      hdm_admin_notice(__( "Please provide a valid php file.", 'hoteldruid-migration' ), 'error');
    }
    hdm_update_option('backup_file_info', print_r($info, true));
  }
  if($value ==  false) {
    $open_basedir=ini_get('open_basedir');
    hdm_admin_notice(sprintf(
      __( "Cannot read %s. Make sure you place your file in a readable directory (open_basedir=%s).", 'hoteldruid-migration' ),
      $value,
      $open_basedir,
    ), 'error');
  }
  return $value;
}

/**
 * Add settings page link in plugin actions on plugins list page
 */
function hoteldruid_migration_settings_link( $links ) {
	$url = esc_url( add_query_arg(
		'page',
		'hoteldruid-migration',
		get_admin_url() . 'tools.php'
	) );

	array_push( $links, "<a href='$url'>" . __('Settings') . "</a>" );

	return $links;
}
add_filter( 'plugin_action_links_hoteldruid-migration/hoteldruid-migration.php', 'hoteldruid_migration_settings_link' );

function hdm_list_accommodations_output() {
  $accommodations = get_transient('hoteldruid_migration_table_accommodations');
  if(!$accommodations) {
    return sprintf(
      __('Select a backup file in settings tab and hit save button to display info', 'hoteldruid-migration'),
      '<a href="#tab-settings">',
      '</a>',
    );
  }
  return render_hdm_list_table($accommodations);
}

function hdm_list_clients_output() {
  $clients = get_transient('hoteldruid_migration_table_clients');
  if(!$clients) {
    return sprintf(
      __('Select a backup file in settings tab and hit save button to display info', 'hoteldruid-migration'),
      '<a href="#tab-settings">',
      '</a>',
    );
  }
  return render_hdm_list_table($clients);
}

function hdm_list_bookings_output() {
  $bookings = get_transient('hoteldruid_migration_table_bookings');
  if(!$bookings) {
    return sprintf(
      __('Select a backup file in settings tab and hit save button to display info', 'hoteldruid-migration'),
      '<a href="#tab-settings">',
      '</a>',
    );
  }
  return render_hdm_list_table($bookings);
}

function hdm_file_info_output() {
  $file = hdm_get_option('hoteldruid_backup_file');
  if(!$file) {
    return '<p>' . __('Select a backup file and hit save button to display info', 'hoteldruid-migration') . '</p>';
  }
  $info = hdm_get_file_info($file);
  $output ="";
  foreach ($info as $key => $value) {
    $output .= "<li><strong>$key:</strong> $value</li>";
  }
  $output = "<ul>$output</ul>";
  return $output;
}
