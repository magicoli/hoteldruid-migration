<?php

function hoteldruid_backup_file_validation($value = NULL, $request = NULL, $param = NULL) {
  if(empty($value)) return NULL;
  if(is_readable($value))   return $value;
  error_log(
    "\nvalue " . print_r($value, true)
    . "\nrequest " . print_r($request, true)
    . "\nparam " . print_r($param, true)
  );
  $open_basedir=ini_get('open_basedir');
  hmigr_admin_notice(sprintf(
    __( "Cannot read %s. Make sure you place your file in a readable directory (open_basedir=%s).", 'hoteldruid-migration' ),
    $value,
    $open_basedir,
  ), 'error');
  // return $value;
}

/**
 * Create settings page
 * @var $settings_pages
 */
add_filter( 'mb_settings_pages', 'hoteldruid_migration_settings_page' );
function hoteldruid_migration_settings_page( $settings_pages ) {
  $settings_pages[] = [
    'menu_title' => __( 'HotelDruid migration', 'hoteldruid-migration' ),
    'id'         => 'hoteldruid-migration',
    'position'   => 25,
    'parent'     => 'tools.php',
    'capability' => 'manage_woocommerce',
    'style'      => 'no-boxes',
    'columns'    => 1,
    'icon_url'   => 'dashicons-admin-generic',
  ];

  return $settings_pages;
}

/**
 * Add fields to settings page
 * @var [type]
 */
add_filter( 'rwmb_meta_boxes', 'hoteldruid_migration_settings_fields' );
function hoteldruid_migration_settings_fields( $meta_boxes ) {
  $prefix = '';

  $meta_boxes[] = [
    'title'          => __( 'HotelDruid migration settings', 'hoteldruid-migration' ),
    'id'             => 'hoteldruid-migration-settings',
    'settings_pages' => ['hoteldruid-migration'],
    'fields'         => [
      [
        'name'              => __( 'HotelDruid backup file location', 'hoteldruid-migration' ),
        'id'                => $prefix . 'hoteldruid_backup_file',
        'type'              => 'file_input',
        'desc'              => __( 'HotelDruid backup file full path. Must be saved in a place readable by the web server, but outside website folder.', 'hoteldruid-migration' ),
        'placeholder'       => __( '/full/path/to/hoteld_backup.php', 'hoteldruid-migration' ),
        'columns'           => 9,
        'sanitize_callback' => 'hoteldruid_backup_file_validation',
      ],
      [
        'name'    => __( 'Import', 'hoteldruid-migration' ),
        'id'      => $prefix . 'import',
        'type'    => 'button',
        'std'     => 'Start conversion',
        'columns' => 3,
        'visible' => [
          'when'     => [['hoteldruid_backup_file', '!=', '']],
          'relation' => 'or',
        ],
      ],
    ],
    'validation'     => [
      'rules'    => [
        $prefix . 'hoteldruid_backup_file' => [
          'extension' => 'php,php.gz',
        ],
      ],
      'messages' => [
        $prefix . 'hoteldruid_backup_file' => [
          'extension' => 'Allowed formats: *.php or *.php.gz',
        ],
      ],
    ],
  ];

  return $meta_boxes;
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
