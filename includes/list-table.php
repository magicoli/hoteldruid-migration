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
        'iddatainizio' => __('iddatainizio', 'hoteldruid-migration'),
        'iddatafine' => __('iddatafine', 'hoteldruid-migration'),
        // 'assegnazioneapp' => __('assegnazioneapp', 'hoteldruid-migration'),
        // 'app_assegnabili' => __('app_assegnabili', 'hoteldruid-migration'),
        'num_persone' => __('num_persone', 'hoteldruid-migration'),
        'cat_persone' => __('cat_persone', 'hoteldruid-migration'),
        // 'idprenota_compagna' => __('idprenota_compagna', 'hoteldruid-migration'),
        // 'tariffa' => __('tariffa', 'hoteldruid-migration'),
        // 'tariffesettimanali' => __('tariffesettimanali', 'hoteldruid-migration'),
        // 'incompatibilita' => __('incompatibilita', 'hoteldruid-migration'),
        'sconto' => __('sconto', 'hoteldruid-migration'),
        'tariffa_tot' => __('tariffa_tot', 'hoteldruid-migration'),
        'caparra' => __('caparra', 'hoteldruid-migration'),
        'commissioni' => __('commissioni', 'hoteldruid-migration'),
        // 'tasseperc' => __('tasseperc', 'hoteldruid-migration'),
        'pagato' => __('pagato', 'hoteldruid-migration'),
        'valuta' => __('valuta', 'hoteldruid-migration'),
        // 'metodo_pagamento' => __('metodo_pagamento', 'hoteldruid-migration'),
        'codice' => __('codice', 'hoteldruid-migration'),
        'origine' => __('origine', 'hoteldruid-migration'),
        'commento' => __('commento', 'hoteldruid-migration'),
        // 'conferma' => __('conferma', 'hoteldruid-migration'),
        // 'checkin' => __('checkin', 'hoteldruid-migration'),
        // 'checkout' => __('checkout', 'hoteldruid-migration'),
        // 'id_anni_prec' => __('id_anni_prec', 'hoteldruid-migration'),
        // 'datainserimento' => __('datainserimento', 'hoteldruid-migration'),
        // 'hostinserimento' => __('hostinserimento', 'hoteldruid-migration'),
        // 'data_modifica' => __('data_modifica', 'hoteldruid-migration'),
        // 'utente_inserimento' => __('utente_inserimento', 'hoteldruid-migration'),
      );
      // error_log(print_r($columns, true));
      break;

      case 'idclienti':
      $columns = array(
        'idclienti' => __('idclienti', 'hoteldruid-migration'),
        'name' => __('name', 'hoteldruid-migration'),
        // 'cognome' => __('cognome', 'hoteldruid-migration'),
        // 'nome' => __('nome', 'hoteldruid-migration'),
        // 'soprannome' => __('soprannome', 'hoteldruid-migration'),
        // 'sesso' => __('sesso', 'hoteldruid-migration'),
        // 'titolo' => __('titolo', 'hoteldruid-migration'),
        'lingua' => __('lingua', 'hoteldruid-migration'),
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
        'cap' => __('cap', 'hoteldruid-migration'),
        'citta' => __('citta', 'hoteldruid-migration'),
        'regione' => __('regione', 'hoteldruid-migration'),
        'nazione' => __('nazione', 'hoteldruid-migration'),
        'telefono' => __('telefono', 'hoteldruid-migration'),
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

      default:
      $columns = array_combine($keys, $keys);
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
      case 'name':
      return trim($item[ 'cognome' ] . ' ' . $item[ 'nome' ]);
      break;

      case 'street':
      return trim($item[ 'numcivico' ] . ' ' . $item[ 'via' ]);
      break;

      case 'nazione':
      $phone = preg_replace('/[^0-9+]/', '', $item['telefono']);
      if(preg_match('/^(0590|0690|\\+590|00590)/', $phone)) return "Guadeloupe";
      if(preg_match('/^(0596|0696|\\+596|00596)/', $phone)) return "Martinique";
      if(preg_match('/^(\\+|00)31/', $phone)) return "Netherlands";
      if(preg_match('/^(\\+|00)32/', $phone)) return "Belgium";
      if(preg_match('/^(\\+|00)33/', $phone)) return "France";
      if(preg_match('/^(\\+|00)44/', $phone)) return "United Kingdom";
      if(preg_match('/^(\\+|00)49/', $phone)) return "Germany";
      if(preg_match('/^0[167][0-9]{8}/', $phone)) return "France";
      if(!empty($item['nazione'])) return $item['nazione'];
      if(!empty($item['nazionalita'])) return $item['nazionalita'];
      if(!empty($item['nazionenascita'])) return $item['nazionenascita'];
      return NULL;
      // case 'id':
      // case 'user_login':
      // case 'user_email':
      // return $item[ $column_name ];
      //
      default:
      return $item[ $column_name ];
      // return print_r( $item, true ) ;
    }
  }

  function prepare_items() {

    $columns = $this->get_columns();
    $hidden = array();
    $sortable = $this->get_sortable_columns();
    $this->_column_headers = array($columns, $hidden, $sortable);
    $this->items = $this->data;
  }
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
