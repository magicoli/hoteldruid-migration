<?php

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
// add_filter( 'rwmb_meta_boxes', 'hoteldruid_migration_settings_fields' );
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
        'name'              => __( 'File info', 'hoteldruid-migration' ),
        'id'                => $prefix . 'backup_file_info',
        'type'              => 'textarea',
        'rows'              => 12,
        'disabled'          => true,
        'readonly'          => true,
        'sanitize_callback' => 'backup_file_info_validation',
        'visible'           => [
          'when'     => [['hoteldruid_backup_file', '!=', '']],
          'relation' => 'and',
        ],
      ],
      [
        'name'    => __( 'Process backup file', 'hoteldruid-migration' ),
        'id'      => $prefix . 'process_backup_file',
        'type'    => 'switch',
        'desc'    => __( 'Check this box to actually import data from the selected HotelDruid backup file.', 'hoteldruid-migration' ),
        'style'   => 'rounded',
        'visible' => [
          'when'     => [['hoteldruid_backup_file', '!=', ''], ['backup_file_info', '!=', '']],
          'relation' => 'and',
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
