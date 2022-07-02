<?php

require_once plugin_dir_path( __FILE__ ) . 'class-list-table.php';
require_once plugin_dir_path( __FILE__ ) . 'functions.php';
require_once plugin_dir_path( __FILE__ ) . 'metabox-definitions.php';

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

function hdm_get_idappartamenti_list() {
  $accommodations = get_transient('hoteldruid_migration_table_accommodations');
  if(empty($accommodations)) return array(false => __('Import HodelDruid data to link product to an accommodation'));
  $values[false] = __('Select an accommodation', 'hoteldruid');
  foreach($accommodations as $acc) {
    $values[$acc['idappartamenti']] = $acc['idappartamenti'];
  }
  return $values;
}

function hdm_get_hdappt_product_id($idappartamenti = NULL) {
  $cache = wp_cache_get('hoteldruid_productid_' . $idappartamenti, 'hoteldruid-migration');
  if($cache !== false) return $cache;
  // global $wpdb;
  $args = array(
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'meta_query' => array(
      array(
        'key' => 'hoteldruid_idappartamenti',
        'value' => $idappartamenti,
      )
    )
  );

  $query = new WP_Query( $args );
  $rows = $query->get_posts();
  // error_log($args['meta_query'][0]['key'] . " = '" .  $args['meta_query'][0]['value'] . "' : " . count($rows) . " rows");
  if( $query->have_posts() ) {
    $product = $query->post;
    $result = $product->ID;
  } else {
    $result = NULL;
  }

  wp_cache_set('hoteldruid_productid_' . $idappartamenti, $result, 'hoteldruid-migration');
  return $result;
}

function hdm_get_hdclient_user_id($idclienti = NULL, $item = NULL) {
  if(is_array($idclienti)) {
    $item = $idclienti;
    $idclienti = $item['idclienti'];
  }
  $cache = wp_cache_get('hoteldruid_userid_' . $idclienti, 'hoteldruid-migration');
  // if($cache !== false) return $cache;
  $result = NULL;

  if(is_email($item['email'])) {
    $user = get_user_by('email', $item['email']);
    if($user) {
      // there is a wp user
      $result = $user->ID;
    }
  }
  // We could also store idclienti and/or compare first and last name
  // but we need a unique email to create wp user anyway, so K.I.S.S.

  wp_cache_set('hoteldruid_userid_' . $idclienti, $result, 'hoteldruid-migration');
  return $result;
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
    foreach($clients as $key => $item) {
      if(empty($item['email'])) continue;
      if(preg_match('/(airbnb.com|booking.com)$/', $item['email'])) continue;
      $user = get_user_by('email', $item['email']);
      if ( ! $user ) {
        // no wp user yet, create one if import_data = 'process'
        $username = (empty($item['displayname'])) ? preg_replace('/@.*/', '', $item['email']) : $item['displayname'];
        $user_login = sanitize_user(preg_replace('/ /', '.', strtolower(
          $username
        )), true);
        $userdata = array(
          // 'ID'                    => 0,    //(int) User ID. If supplied, the user will be updated.
          // 'user_pass'             => '',   //(string) The plain-text user password.
          'user_login'            => $user_login,   //(string) The user's login username.
          // 'user_nicename'         => '',   //(string) The URL-friendly user name.
          // 'user_url'              => '',   //(string) The user URL.
          'user_email'            => $item['email'],   //(string) The user email address.
          'display_name'          => $item['displayname'],   //(string) The user's display name. Default is the user's username.
          // 'nickname'              => '',   //(string) The user's nickname. Default is the user's username.
          'first_name'            => $item['firstname'],   //(string) The user's first name. For new users, will be used to build the first part of the user's display name if $display_name is not specified.
          'last_name'             => $item['lastname'],   //(string) The user's last name. For new users, will be used to build the second part of the user's display name if $display_name is not specified.
          // 'description'           => '',   //(string) The user's biographical description.
          // 'rich_editing'          => '',   //(string|bool) Whether to enable the rich-editor for the user. False if not empty.
          // 'syntax_highlighting'   => '',   //(string|bool) Whether to enable the rich code editor for the user. False if not empty.
          // 'comment_shortcuts'     => '',   //(string|bool) Whether to enable comment moderation keyboard shortcuts for the user. Default false.
          // 'admin_color'           => '',   //(string) Admin color scheme for the user. Default 'fresh'.
          // 'use_ssl'               => '',   //(bool) Whether the user should always access the admin over https. Default false.
          // 'user_registered'       => '',   //(string) Date the user registered. Format is 'Y-m-d H:i:s'.
          'show_admin_bar_front'  => false,   //(string|bool) Whether to display the Admin Bar for the user on the site's front end. Default true.
          'role'                  => 'customer',   //(string) User's role.
          'locale'                => $item['lingua'],   //(string) User's locale. Default empty.
        );

        if ( empty($item['firstname']) || empty($item['lastname'])) {
          $billing_company = $item['displayname'];
          $billing_first_name = NULL;
          $billing_last_name = NULL;
        } else {
          $billing_first_name = $item['firstname'];
          $billing_last_name = $item['lastname'];
          $billing_company = NULL;
        }
        $usermeta = array(
          'hoteldruid_idclienti' => $item['idclienti'],
          'billing_first_name' => $billing_last_name,
          'billing_last_name' => $billing_first_name,
          'billing_company' => $billing_company,
          'billing_address_1' => $item['street'],
          'billing_city' => $item['citta'],
          'billing_postcode' => $item['cap'],
          'billing_state' => $item['regione'],
          'billing_country' => $item['country'],
          'billing_phone' => $item['phone'],
          'billing_email' => $item['email'],
        );

        $user_id = wp_insert_user( $userdata );
        if($user_id) {
          foreach ($usermeta as $meta_key => $meta_value) {
            update_user_meta($user_id, $meta_key, $meta_value);
          }
        }
      }
    }

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
    $countrycodes = array(
      'United States Of America' => 'US',
      '0' => '',
      'Guadeloupe' => 'GP',
      'France' => 'FR',
      'Belgium' => 'BE',
      'Switzerland' => 'CH',
      'Martinique' => 'MQ',
      'United Kingdom' => 'MF',
      'Czech Republic' => 'CZ',
      'Belgique' => 'BE',
      'Royaume Uni' => 'GB',
      'Sweden' => 'SE',
      'Germany' => 'DE',
      "C\xc3\xb4te D'Ivoire" => 'CI',
      'Slovenia' => 'SI',
      'Canada' => 'CA',
      'Netherlands' => 'NL',
      'Italy' => 'IT',
      'Cz' => 'CZ',
      'Fr' => 'FR',
      'Us' => 'US',
      'Ca' => 'CA',
      'Dm' => 'DM',
      'Luxembourg' => 'LU',
      'Ch' => 'CH',
      'Se' => 'SE',
      'Poland' => 'PL',
      'Austria' => 'AT',
      'Ro' => 'RO',
      'Romania' => 'RO',
      'Saint Martin' => 'SF',
      'Pl' => 'PL',
    );
    $languagecodes = array(
      'en' => 'en',
      'fr' => 'fr_FR',
      'de' => 'de_DE',
    );
    foreach( $dom->getElementsByTagName('tabella') as $node) {
      $tablename = $node->getElementsByTagName('nometabella')->item(0)->nodeValue;
      // error_log("tablename " . $tablename);

      switch(preg_replace('/[0-9]{4}$/', '', $tablename)) {
        case 'clienti':
        $items = flatten_array(node2array($node));
        $data = [];
        foreach ($items as $item) {
          $key = trim($item['idclienti']);
          if(empty($key)) continue; // should not happen, just to be sure

          $phone = preg_replace('/[^0-9+]/', '', $item['telefono']);
          $item['country'] = $item['nazione'];
          if(preg_match('/^(0590|0690|\\+590|00590)/', $phone)) $item['country'] = "Guadeloupe";
          else if(preg_match('/^(0596|0696|\\+596|00596)/', $phone)) $item['country'] = "Martinique";
          else if(preg_match('/^(\\+|00)31/', $phone)) $item['country'] = "Netherlands";
          else if(preg_match('/^(\\+|00)32/', $phone)) $item['country'] = "Belgique";
          else if(preg_match('/^(\\+|00)33/', $phone)) $item['country'] = "France";
          else if(preg_match('/^(\\+|00)44/', $phone)) $item['country'] = "Royaume Uni";
          else if(preg_match('/^(\\+|00)49/', $phone)) $item['country'] = "Germany";
          else if(preg_match('/\.de$/', $item['email'])) $item['country'] = "Germany";
          else if(preg_match('/\.be$/', $item['email'])) $item['country'] = "Belgique";
          else if(preg_match('/^0[167][0-9]{8}/', $phone)) $item['country'] = "France";
          else if(!empty($item['country'])) $item['country'] = $item['country'];
          else if(!empty($item['nazionalita'])) $item['country'] = $item['nazionalita'];
          else if(!empty($item['nazionenascita'])) $item['country'] = $item['nazionenascita'];
          $item['phone'] = join(', ', array_filter([ $item['telefono'], $item['telefono2'], $item['telefono3'] ]));
          $emails = [ $item['email'], $item['email2'], $item['email3'] ];
          $emails = preg_replace('/.*@(guest.airbnb.com|reply.airbnb.com|guest.booking.com)/', '', $emails);
          $item['email'] = join(', ', array_filter( $emails ));
          unset($item['telefono'], $item['telefono2'], $item['telefono3']);
          $item['lastname'] = trim($item[ 'cognome' ]);
          $item['firstname'] = trim($item[ 'nome' ]);
          $item['displayname'] = trim($item[ 'firstname' ] . ' ' . $item[ 'lastname' ]);
          $item['street'] = preg_replace('/ Rue$/', '', trim($item[ 'numcivico' ] . ' ' . $item[ 'via' ]));

          if(isset($countrycodes[$item['country']])) $item['country'] = $countrycodes[$item['country']];
          if(isset($languagecodes[$item['lingua']])) $item['lingua'] = $languagecodes[$item['lingua']];

          if(isset($data[$key])) {
            $item = array_merge($data[$key], array_filter($item));
          }
          $data[$key] = $item;
        }
        $clients = $data;
        break;

        case 'appartamenti':
        $items = flatten_array(node2array($node));
        $data = [];
        foreach ($items as $item) {
          $key = $item['idappartamenti'];
          $item['product_id'] = hdm_get_hdappt_product_id($key);
          $data[$key] = $item;
        }
        $accommodations = $data;
        break;

        case 'prenota':
        $data = [];
        $year=preg_replace('/.*([0-9]{4})$/', '$1', $tablename);
        $items = flatten_array(node2array($node));
        foreach ($items as $key => $item) {
          $key = $year * 10000 + $item['idprenota'];
          $item['key'] = $key;
          $item['year'] = $year;
          // $item['idprenota'] = $year * 10000 + $item['idprenota'];
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

          $data[$key] = $item;
        }
        if(!empty($data)) {
          ksort($data);
          $bookings = array_merge($bookings, $data);
          $years[] = $year;
        }
        break;

        // default:
        // $tables[$tablename] = $data;
      }
    }
    ksort($clients, SORT_NUMERIC);
    ksort($bookings, SORT_NUMERIC);

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

  if(is_array($clients)) $info['clients'] = count($clients);
  if(is_array($bookings)) $info['bookings'] = count($bookings);
  if(is_array($accommodations)) $info['accommodations'] = count($accommodations);
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
