<?php

add_filter( 'mb_settings_pages', 'hoteldruid_migration_settings_page' );
function hoteldruid_migration_settings_page( $settings_pages ) {
  $settings_pages['hoteldruid-migration'] = [
    'menu_title' => __( 'HotelDruid migration', 'hoteldruid-migration' ),
    'id'         => 'hoteldruid-migration',
    'position'   => 25,
    'parent'     => 'tools.php',
    'capability' => 'manage_woocommerce',
    'style'      => 'no-boxes',
    'columns'    => 1,
    // 'tabs'       => [
    //   'settings'       => 'Settings',
    //   'accommodations' => 'Accomodations',
    //   'clients'        => 'Clients',
    //   'bookings'       => 'Bookings',
    // ],
    'icon_url'   => 'dashicons-admin-generic',
  ];

  if(!empty(hdm_get_option('import_data'))) {
    $settings_pages['hoteldruid-migration']['tabs'] = [
      'settings'       => 'Settings',
      'accommodations' => 'Accomodations',
      'clients'        => 'Clients',
      'bookings'       => 'Bookings',
    ];
  }

  return $settings_pages;
}

add_filter( 'rwmb_meta_boxes', 'hoteldruid_migration_settings_fields' );
function hoteldruid_migration_settings_fields( $meta_boxes ) {
  $prefix = '';

  $meta_boxes[] = [
    'title'          => __( 'Settings', 'hoteldruid-migration' ),
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
      [
        'name'              => __( 'Import data in WooCommerce', 'hoteldruid-migration' ),
        'id'                => $prefix . 'import_data',
        'type'              => 'button_group',
        'options'           => hdm_import_button_values(),
        'sanitize_callback' => 'import_data_field_validation',
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


if(!empty(hdm_get_option('import_data')))
add_filter( 'rwmb_meta_boxes', 'hoteldruid_migration_tab_accommodations' );
function hoteldruid_migration_tab_accommodations( $meta_boxes ) {
  $prefix = '';

  $meta_boxes[] = [
    'title'          => __( 'Accommodations', 'hoteldruid-migration' ),
    'id'             => 'hoteldruid-migration-accommodations',
    'settings_pages' => ['hoteldruid-migration'],
    'tab'            => 'accommodations',
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

if(!empty(hdm_get_option('import_data')))
add_filter( 'rwmb_meta_boxes', 'hoteldruid_migration_tab_clients' );
function hoteldruid_migration_tab_clients( $meta_boxes ) {
  $prefix = '';

  $meta_boxes[] = [
    'title'          => __( 'Clients', 'hoteldruid-migration' ),
    'id'             => 'hoteldruid-migration-clients',
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

if(!empty(hdm_get_option('import_data')))
add_filter( 'rwmb_meta_boxes', 'hoteldruid_migration_tab_bookings' );
function hoteldruid_migration_tab_bookings( $meta_boxes ) {
  $prefix = '';

  $meta_boxes[] = [
    'title'          => __( 'Bookings', 'hoteldruid-migration' ),
    'id'             => 'hoteldruid-migration-bookings',
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

add_filter( 'rwmb_meta_boxes', 'hoteldruid_migration_product_hoteldruid_link' );
function hoteldruid_migration_product_hoteldruid_link( $meta_boxes ) {
  $prefix = '';

  $meta_boxes[] = [
    'title'      => __( 'HotelDruid links', 'hoteldruid-migration' ),
    'id'         => 'product-hoteldruid-link',
    'post_types' => ['product'],
    'context'    => 'side',
    'autosave'   => true,
    'include'    => [
      'relation'     => 'OR',
      'product_type' => [125],
    ],
    'fields'     => [
      [
        'name'    => __( 'HotelDruid idappartementi', 'hoteldruid-migration' ),
        'id'      => $prefix . 'hoteldruid_idappartementi',
        'type'    => 'select',
        'options' => hdm_get_idappartamenti_list(),
      ],
    ],
  ];

  return $meta_boxes;
}
