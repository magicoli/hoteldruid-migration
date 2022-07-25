<?php

function import_data_field_validation($value = NULL, $request = NULL, $param = NULL) {
  $file = hdm_get_option('hoteldruid_backup_file', NULL);
  $clients = get_transient('hoteldruid_migration_table_clients');
  $bookings = get_transient('hoteldruid_migration_table_bookings');
  $accommodations = get_transient('hoteldruid_migration_table_accommodations');
  if (empty($clients) || empty($accommodations) || empty($bookings) || empty(hdm_get_option('hoteldruid_backup_file'))  )
  return '';
  if ( hdm_get_option('import_data_processed' ) == true) return  'processed';

  if($value == 'process') {
    error_log('Processing import file');
    foreach($clients as $key => $item) {
      $user = false;
      if(preg_match('/(airbnb.com|booking.com)$/', $item['email'])) $item['email'] = NULL;
      $user_login = hdm_create_user_login($item);

      if(!empty($item['email'])) {
        $user = get_user_by('email', $item['email']);
      } else if(!empty($user_login)) {
        $user = get_user_by('user_login', $user_login);
      }

      if ( $user ) {
        $user_id = $user->ID;
      } else {
        // no wp user yet, create one if import_data = 'process'
        $userdata = array(
          // 'ID'                    => 0,    //(int) User ID. If supplied, the user will be updated.
          'user_pass'             => NULL,   //(string) The plain-text user password.
          'user_login'            => (!empty($user->user_login)) ? $user->user_login : $user_login,   //(string) The user's login username.
          // 'user_nicename'         => '',   //(string) The URL-friendly user name.
          // 'user_url'              => '',   //(string) The user URL.
          'user_email'            => $item['email'],   //(string) The user email address.
          'display_name'          => (!empty($user->display_name)) ? $user->display_name : $item['displayname'],   //(string) The user's display name. Default is the user's username.
          // 'nickname'              => '',   //(string) The user's nickname. Default is the user's username.
          'first_name'            => (!empty($user->first_name)) ? $user->first_name : $item['firstname'],   //(string) The user's first name. For new users, will be used to build the first part of the user's display name if $display_name is not specified.
          'last_name'             => (!empty($user->last_name)) ? $user->last_name : $item['lastname'],   //(string) The user's last name. For new users, will be used to build the second part of the user's display name if $display_name is not specified.
          // 'description'           => '',   //(string) The user's biographical description.
          // 'rich_editing'          => '',   //(string|bool) Whether to enable the rich-editor for the user. False if not empty.
          // 'syntax_highlighting'   => '',   //(string|bool) Whether to enable the rich code editor for the user. False if not empty.
          // 'comment_shortcuts'     => '',   //(string|bool) Whether to enable comment moderation keyboard shortcuts for the user. Default false.
          // 'admin_color'           => '',   //(string) Admin color scheme for the user. Default 'fresh'.
          // 'use_ssl'               => '',   //(bool) Whether the user should always access the admin over https. Default false.
          // 'user_registered'       => '',   //(string) Date the user registered. Format is 'Y-m-d H:i:s'.
          'show_admin_bar_front'  => false,   //(string|bool) Whether to display the Admin Bar for the user on the site's front end. Default true.
          'role'                  => 'customer',   //(string) User's role.
          'locale'                => (!empty($user->locale)) ? $user->locale : $item['lingua'],   //(string) User's locale. Default empty.
        );
        $user_id = wp_insert_user( $userdata );

        if($user_id) {
          if ( empty($item['firstname']) || empty($item['lastname'])) {
            $billing_company = $item['displayname'];
            $billing_first_name = NULL;
            $billing_last_name = NULL;
          } else {
            $billing_first_name = $item['firstname'];
            $billing_last_name = $item['lastname'];
            $billing_company = NULL;
          }
          // error_log(print_r(get_user_meta($user_id), true));
          $usermeta = array(
            'hoteldruid_idclienti' => $item['idclienti'],
            'billing_first_name' => $billing_first_name,
            'billing_last_name' => $billing_last_name,
            'billing_company' => $billing_company,
            'billing_address_1' => $item['street'],
            'billing_city' => $item['citta'],
            'billing_postcode' => $item['cap'],
            'billing_state' => $item['regione'],
            'billing_country' => $item['country'],
            'billing_phone' => $item['phone'],
            'billing_email' => $item['email'],
          );

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
