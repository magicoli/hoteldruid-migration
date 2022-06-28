<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class HDM_List_Table extends WP_List_Table {
  var $data = [];

  public function __construct($data = [])
  {
    parent::__construct( array(
      'singular'=> 'wp_list_text_link', //Singular label
      'plural' => 'wp_list_test_links', //plural label, also this well be one of the table css class
      'ajax'   => false //We won't support Ajax for this table
    ) );
    $this->data = $data;
    $this->prepare_items();
    $this->display();
  }

  function get_columns() {
    $rows = $this->data;
    if(count($rows) == 0) return [ 'empty' => __('Empty table') ];

    // $first_row = ;
    $keys = array_keys(array_shift($rows));
    switch ($keys[0]) {
      case 'idprenota':
      $columns = array (
        'idprenota' => __('idprenota', 'hoteldruid-migration'),
        'idclienti' => __('idclienti', 'hoteldruid-migration'),
        'idappartamenti' => __('idappartamenti', 'hoteldruid-migration'),
        // 'iddatainizio' => __('iddatainizio', 'hoteldruid-migration'),
        // 'iddatafine' => __('iddatafine', 'hoteldruid-migration'),
        'arrival' => __('arrival', 'hoteldruid-migration'),
        'departure' => __('departure', 'hoteldruid-migration'),
        'nights' => __('nights', 'hoteldruid-migration'),
        // 'assegnazioneapp' => __('assegnazioneapp', 'hoteldruid-migration'),
        // 'app_assegnabili' => __('app_assegnabili', 'hoteldruid-migration'),
        'num_persone' => __('guests', 'hoteldruid-migration'),
        // 'cat_persone' => __('cat_persone', 'hoteldruid-migration'),
        'adults' => __('adults', 'hoteldruid-migration'),
        'children' => __('children', 'hoteldruid-migration'),
        // 'idprenota_compagna' => __('idprenota_compagna', 'hoteldruid-migration'),
        // 'tariffa' => __('tariffa', 'hoteldruid-migration'),
        // 'tariffesettimanali' => __('tariffesettimanali', 'hoteldruid-migration'),
        // 'incompatibilita' => __('incompatibilita', 'hoteldruid-migration'),
        'sconto' => __('discount', 'hoteldruid-migration'),
        'tariffa_tot' => __('total', 'hoteldruid-migration'),
        'caparra' => __('deposit', 'hoteldruid-migration'),
        'commissioni' => __('commission', 'hoteldruid-migration'),
        // 'tasseperc' => __('tasseperc', 'hoteldruid-migration'),
        'pagato' => __('paid', 'hoteldruid-migration'),
        // 'valuta' => __('valuta', 'hoteldruid-migration'),
        // 'metodo_pagamento' => __('metodo_pagamento', 'hoteldruid-migration'),
        'codice' => __('code', 'hoteldruid-migration'),
        'origine' => __('origin', 'hoteldruid-migration'),
        'commento' => __('comment', 'hoteldruid-migration'),
        // 'conferma' => __('conferma', 'hoteldruid-migration'),
        // 'checkin' => __('checkin', 'hoteldruid-migration'),
        // 'checkout' => __('checkout', 'hoteldruid-migration'),
        // 'id_anni_prec' => __('id_anni_prec', 'hoteldruid-migration'),
        // 'datainserimento' => __('datainserimento', 'hoteldruid-migration'),
        // 'hostinserimento' => __('hostinserimento', 'hoteldruid-migration'),
        // 'data_modifica' => __('data_modifica', 'hoteldruid-migration'),
        // 'utente_inserimento' => __('utente_inserimento', 'hoteldruid-migration'),
      );
      // $columns = array_combine($keys, $keys);
      break;

      case 'idclienti':
      $columns = array(
        'idclienti' => __('idclienti', 'hoteldruid-migration'),
        'displayname' => __('name', 'hoteldruid-migration'),
        // 'cognome' => __('cognome', 'hoteldruid-migration'),
        // 'nome' => __('nome', 'hoteldruid-migration'),
        // 'soprannome' => __('soprannome', 'hoteldruid-migration'),
        // 'sesso' => __('sesso', 'hoteldruid-migration'),
        // 'titolo' => __('titolo', 'hoteldruid-migration'),
        'lingua' => __('lang', 'hoteldruid-migration'),
        // 'datanascita' => __('datanascita', 'hoteldruid-migration'),
        // 'cittanascita' => __('cittanascita', 'hoteldruid-migration'),
        // 'regionenascita' => __('regionenascita', 'hoteldruid-migration'),
        // 'nazionenascita' => __('nazionenascita', 'hoteldruid-migration'),
        // 'documento' => __('documento', 'hoteldruid-migration'),
        // 'scadenzadoc' => __('scadenzadoc', 'hoteldruid-migration'),
        // 'tipodoc' => __('tipodoc', 'hoteldruid-migration'),
        // 'cittadoc' => __('cittadoc', 'hoteldruid-migration'),
        // 'regionedoc' => __('regionedoc', 'hoteldruid-migration'),
        // 'nazionedoc' => __('nazionedoc', 'hoteldruid-migration'),
        // 'nazionalita' => __('nazionalita', 'hoteldruid-migration'),
        'street' => __('street', 'hoteldruid-migration'),
        // 'via' => __('via', 'hoteldruid-migration'),
        // 'numcivico' => __('numcivico', 'hoteldruid-migration'),
        'cap' => __('zip', 'hoteldruid-migration'),
        'citta' => __('city', 'hoteldruid-migration'),
        'regione' => __('region', 'hoteldruid-migration'),
        // 'nazione' => __('nazione', 'hoteldruid-migration'),
        'country' => __('country', 'hoteldruid-migration'),
        'phone' => __('phone', 'hoteldruid-migration'),
        // 'telefono' => __('telefono', 'hoteldruid-migration'),
        // 'telefono2' => __('telefono2', 'hoteldruid-migration'),
        // 'telefono3' => __('telefono3', 'hoteldruid-migration'),
        // 'fax' => __('fax', 'hoteldruid-migration'),
        'email' => __('email', 'hoteldruid-migration'),
        // 'email2' => __('email2', 'hoteldruid-migration'),
        // 'email3' => __('email3', 'hoteldruid-migration'),
        // 'cod_fiscale' => __('cod_fiscale', 'hoteldruid-migration'),
        // 'partita_iva' => __('partita_iva', 'hoteldruid-migration'),
        // 'commento' => __('commento', 'hoteldruid-migration'),
        // 'max_num_ordine' => __('max_num_ordine', 'hoteldruid-migration'),
        // 'idclienti_compagni' => __('idclienti_compagni', 'hoteldruid-migration'),
        // 'doc_inviati' => __('doc_inviati', 'hoteldruid-migration'),
        // 'datainserimento' => __('datainserimento', 'hoteldruid-migration'),
        // 'hostinserimento' => __('hostinserimento', 'hoteldruid-migration'),
        // 'utente_inserimento' => __('utente_inserimento', 'hoteldruid-migration'),
      );
      break;

      case 'idappartamenti':
      $columns = array (
        'idappartamenti' => __('idappartamenti', 'hoteldruid-migration'),
        'product_id' => __('Product', 'hoteldruid-migration'),
        // 'numpiano' => __('numpiano', 'hoteldruid-migration'),
        'maxoccupanti' => __('max occupancy', 'hoteldruid-migration'),
        // 'numcasa' => __('numcasa', 'hoteldruid-migration'),
        // 'app_vicini' => __('app_vicini', 'hoteldruid-migration'),
        'priorita' => __('priority', 'hoteldruid-migration'),
        'priorita2' => __('priority 2', 'hoteldruid-migration'),
        // 'letto' => __('letto', 'hoteldruid-migration'),
        'commento' => __('comment', 'hoteldruid-migration'),
      );
      break;

      default:
      $columns = array_combine($keys, $keys);
      error_log(print_r($columns, true));
    }
    // unset($columns['numpiano'], $columns['app_vicini']);
    // $columns = $keys;
    // $columns = array(
    //   'id'    => 'ID',
    //   'user_login'     => 'User Name',
    //   'user_email'   => 'User Email'
    // );
    return $columns;
  }

  function column_default( $item, $column_name ) {
    switch( $column_name ) {
      case 'product_id':
      // $product_id = hdm_get_hdappt_product_id($item['idappartamenti']);
      if(!empty($item['product_id']) && is_integer($item['product_id'])) {
        return sprintf(
          '<a href="%s">%s</a>',
          get_permalink($item['product_id']),
          get_the_title($item['product_id']),
        );
      }
      return;

      default:
      return $item[ $column_name ];
    }
  }

  function prepare_items() {

    $columns = $this->get_columns();
    $hidden = array();
    $sortable = $this->get_sortable_columns();
    $this->_column_headers = array($columns, $hidden, $sortable);
    // usort( $this->data, array( &$this, 'usort_reorder' ) );
    $this->items = $this->data;
  }

  /**
   * Sort doesn't seem work in tabbed settings page context
   * It's not a big deal in this case, so I won't fix it
  **/

  // function get_sortable_columns() {
  //   $sortable_columns = array(
  //     'idclienti'  => array('idclienti',false),
  //     'idappartamenti'  => array('idappartamenti',false),
  //     'name'  => array('name',false),
  //   );
  //   return $sortable_columns;
  // }
  //
  // function usort_reorder( $a, $b ) {
  //   // If no sort, default to title
  //   $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'name';
  //   // If no order, default to asc
  //   $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
  //   // Determine sort order
  //   $result = strcmp( $a[$orderby], $b[$orderby] );
  //   // Send final sort direction to usort
  //   return ( $order === 'asc' ) ? $result : -$result;
  // }

}

// Render your page outside the class
function render_hdm_list_table($data=[]) {
  global $wpdb;

  $Obj_HDM_List_Table=new HDM_List_Table($data);
  $Obj_HDM_List_Table->prepare_items();

  // $sql="SELECT * from {$wpdb->prefix}users";
  // $sql_result=$wpdb->get_results($sql,'ARRAY_A');
  // print_r($sql_result);
}
