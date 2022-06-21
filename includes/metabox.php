<?php

require_once plugin_dir_path( __FILE__ ) . 'metabox-definitions.php';
require_once plugin_dir_path( __FILE__ ) . 'list-table.php';

function hdm_get_option( $option, $default = false ) {
  $value = rwmb_meta( $option, ['object_type' => 'setting'], 'hoteldruid-migration' );
  if($value === NULL) return $default;
  return $value;
}

function hdm_update_option( $option, $value, $autoload = null, $args=[] ) {
  rwmb_set_meta( 'hoteldruid-migration', $option, $value, $args );
}

function node2array($node)
{
    $array = false;
    if ($node->hasAttributes())
    {
        foreach ($node->attributes as $attr)
        {
            $array[$attr->nodeName] = $attr->nodeValue;
        }
    }

    if ($node->hasChildNodes())
    {
        if ($node->childNodes->length == 1)
        {
            $array[$node->firstChild->nodeName] = $node->firstChild->nodeValue;
        }
        else
        {
            foreach ($node->childNodes as $childNode)
            {
                if ($childNode->nodeType != XML_TEXT_NODE)
                {
                    $array[$childNode->nodeName][] = node2array($childNode);
                }
            }
        }
    }

    return $array;
}

function flatten_array($data) {
  if(!isset($data['colonnetabella'][0]['nomecolonna'])) return $data;
  if(!isset($data['righetabella'][0]['riga'])) return [];
  $rows = array();
  $keys = array_map('join', $data['colonnetabella'][0]['nomecolonna']);
  $values = $data['righetabella'][0]['riga'];
  foreach($values as $key=>$value) {
    $row = array_combine($keys, $value['cmp']);
    if(is_array($row)) {
      $row = array_map('join_if_array', $row);
      $rows[] = array_map('join_if_array', $row);
    }
  }
  return $rows;
}

function join_if_array($value) {
  if(is_array($value)) return join($value);
  return $value;
}

function hdm_get_file_info($file) {
  $cache = wp_cache_get('hdm_backup_file_info', 'hoteldruid-migration');
  if(!empty($cache)) return $cache;
  if(empty($file)) return false;
  if(!is_file($file)) return false;
  if(!is_readable($file)) return false;
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
        $clients = flatten_array(node2array($node));
        break;

        case 'appartamenti':
        $accommodations = flatten_array(node2array($node));
        break;

        case 'prenota':
        $year=preg_replace('/.*([0-9]{4})$/', '$1', $tablename);
        $data = flatten_array(node2array($node));
        if(!empty($data)) {
          $bookings = array_merge($bookings, $data);
          $years[] = $year;
        }
        break;

        // default:
        // $tables[$tablename] = $data;
      }
    }
    if(!empty($clients)) set_transient('hoteldruid_migration_table_clients', $clients, 86400);
    if(!empty($bookings)) set_transient('hoteldruid_migration_table_bookings', $bookings, 86400);
    if(!empty($years)) set_transient('hoteldruid_migration_table_years', $years, 86400);
    if(!empty($accommodations)) set_transient('hoteldruid_migration_table_accommodations', $accommodations, 86400);
  }

  $info['clients'] = count($clients);
  $info['bookings'] = count($bookings);
  $info['accommodations'] = count($accommodations);
  if(!empty($years)) $info['years'] = count($years) . " (from " . min($years) . " to " . max($years) . ")";

  wp_cache_set('hdm_backup_file_info', $info, 'hoteldruid-migration');
  wp_cache_set('hdm_data_clients', $clients, 'hoteldruid-migration');
  wp_cache_set('hdm_data_bookings', $bookings, 'hoteldruid-migration');
  return $info;
}

function backup_file_info_validation($value = NULL, $request = NULL, $param = NULL) {
  $file = hdm_get_option('hoteldruid_backup_file');
  return print_r(hdm_get_file_info($file), true);
}

function hoteldruid_backup_file_validation($value = NULL, $request = NULL, $param = NULL) {
  if(empty($value)) return NULL;
  $info = hdm_get_file_info($value);
  if($info != false && $info != null) {
    if($info['mime'] != 'text/x-php') {
      hdm_admin_notice(__( "Please provide a valid php file.", 'hoteldruid-migration' ), 'error');
      return $value;
    }
    hdm_update_option('backup_file_info', print_r($info, true));
    return $value;
  }
  $open_basedir=ini_get('open_basedir');
  hdm_admin_notice(sprintf(
    __( "Cannot read %s. Make sure you place your file in a readable directory (open_basedir=%s).", 'hoteldruid-migration' ),
    $value,
    $open_basedir,
  ), 'error');
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
    $output .= "<li><strong>$key:</strong>$value</li>";
  }
  $output = "<ul>$output</ul>";
  return $output;
}
