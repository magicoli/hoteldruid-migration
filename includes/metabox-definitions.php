<?php
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
    'tabs'       => [
      'settings'       => 'Settings',
      'accommodations' => 'Accomodations',
      'clients'        => 'Clients',
      'bookings'       => 'Bookings',
    ],
    'icon_url'   => 'dashicons-admin-generic',
  ];

  return $settings_pages;
}

add_filter( 'rwmb_meta_boxes', 'hoteldruid_migration_settings_fields' );
function hoteldruid_migration_settings_fields( $meta_boxes ) {
  $prefix = '';

  $meta_boxes[] = [
    'title'          => __( 'HotelDruid migration settings', 'hoteldruid-migration' ),
    'id'             => 'hoteldruid-migration-settings',
    'settings_pages' => ['hoteldruid-migration'],
    'tab'            => 'settings',
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
        'name'     => __( 'File info', 'hoteldruid-migration' ),
        'id'       => $prefix . 'file_info',
        'type'     => 'custom_html',
        'callback' => 'hdm_file_info_output',
        'visible'  => [
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

add_filter( 'rwmb_meta_boxes', 'hoteldruid_migration_tab_accommodations' );
function hoteldruid_migration_tab_accommodations( $meta_boxes ) {
  $prefix = '';

  $meta_boxes[] = [
    'title'          => __( 'Accommodations', 'hoteldruid-migration' ),
    'id'             => 'tab-accommodations',
    'settings_pages' => ['hoteldruid-migration'],
    'tab'            => 'accommodations',
    'visible'        => [
      'when'     => [['hoteldruid_backup_file', '!=', ''], ['backup_file_info', '!=', '']],
      'relation' => 'and',
    ],
    'fields'         => [
      [
        'id'       => $prefix . 'hdm_list_accommodations',
        'type'     => 'custom_html',
        'callback' => 'hdm_list_accommodations_output',
      ],
    ],
  ];

  return $meta_boxes;
}

add_filter( 'rwmb_meta_boxes', 'hoteldruid_migration_tab_clients' );
function hoteldruid_migration_tab_clients( $meta_boxes ) {
  $prefix = '';

  $meta_boxes[] = [
    'title'          => __( 'Clients', 'hoteldruid-migration' ),
    'id'             => 'tab-clients',
    'settings_pages' => ['hoteldruid-migration'],
    'tab'            => 'clients',
    'fields'         => [
      [
        'id'       => $prefix . 'hdm_list_clients',
        'type'     => 'custom_html',
        'callback' => 'hdm_list_clients_output',
      ],
    ],
  ];

  return $meta_boxes;
}

add_filter( 'rwmb_meta_boxes', 'hoteldruid_migration_tab_bookings' );
function hoteldruid_migration_tab_bookings( $meta_boxes ) {
  $prefix = '';

  $meta_boxes[] = [
    'title'          => __( 'Bookings', 'hoteldruid-migration' ),
    'id'             => 'tab-bookings',
    'settings_pages' => ['hoteldruid-migration'],
    'tab'            => 'bookings',
    'fields'         => [
      [
        'id'       => $prefix . 'hdm_list_bookings',
        'type'     => 'custom_html',
        'callback' => 'hdm_list_bookings_output',
      ],
    ],
  ];

  return $meta_boxes;
}
