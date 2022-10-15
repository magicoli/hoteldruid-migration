<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://github.com/magicoli/multipass
 * @since      0.1.0
 *
 * @package    MultiPass
 * @subpackage MultiPass/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    MultiPass
 * @subpackage MultiPass/includes
 * @author     Magiiic <info@magiiic.com>
 */
class Mltp_HotelDruid extends Mltp_Modules {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    0.1.0
	 */
	public function __construct() {
		$this->module = 'hoteldruid';
		$this->name   = 'HotelDruid';

		$this->clienti = get_transient( 'hoteldruid_migration_table_clients' );
		if ( empty( $this->clienti ) ) {
			$this->clienti = array();
		}
		$this->prenota = get_transient( 'hoteldruid_migration_table_bookings' );
		if ( empty( $this->prenota ) ) {
			$this->prenota = array();
		}
		$this->appartementi = get_transient( 'hoteldruid_migration_table_accommodations' );
		if ( empty( $this->appartementi ) ) {
			$this->appartementi = array();
		}

		$this->countrycodes = array(
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
		$this->languagecodes = array(
			'en' => 'en',
			'fr' => 'fr_FR',
			'de' => 'de_DE',
		);

		// register_activation_hook( MULTIPASS_FILE, __CLASS__ . '::activate' );
		// register_deactivation_hook( MULTIPASS_FILE, __CLASS__ . '::deactivate' );
	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    0.1.0
	 */
	public function init() {

		$this->actions = array(
			// array(
			// 'hook'          => 'wp_insert_post',
			// 'callback'      => 'wp_insert_post_action',
			// 'accepted_args' => 3,
			// ),
			// array(
			// 'hook'          => 'save_post_shop_order',
			// 'hook'          => 'save_post', // use save_post because save_post_prestation_item is fired before actual save and meta values are not yet updated.
			// 'callback'      => 'save_post_action',
			// 'accepted_args' => 3,
			// ),
		);

		$this->filters = array(
			array(
				'hook'     => 'mb_settings_pages',
				'callback' => 'register_settings_pages',
			),
			array(
				'hook'     => 'rwmb_meta_boxes',
				'callback' => 'register_settings_fields',
			),
			array(
				'hook'     => 'rwmb_meta_boxes',
				'callback' => 'register_fields',
			),
			array(
				'hook'     => 'multipass_register_terms_mltp_detail-source',
				'callback' => 'register_sources_filter',
			),
		//
			// array(
			// 'hook'     => 'multipass_update_resource_title',
			// 'callback' => 'update_resource_title',
			// ),
		//
		//
			// array(
			// 'hook'          => 'manage_prestation_posts_custom_column',
			// 'callback'      => 'prestations_columns_display',
			// 'accepted_args' => 2,
			// ),
		);

		$defaults = array(
			'component'     => $this,
			'priority'      => 10,
			'accepted_args' => 1,
		);

		foreach ( $this->filters as $hook ) {
			$hook = array_merge( $defaults, $hook );
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			$hook = array_merge( $defaults, $hook );
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

	}

	function activate() {
		// self::import_now();
	}

	/**
	 * Add HotelDruid tab to settings page. 'HotelDruid' string is intentionally
	 * left not translatable as it is a brand name.
	 *
	 * @param  array $settings_pages  Current settings.
	 * @return array                  Updated settings.
	 */
	function register_settings_pages( $settings_pages ) {
		$settings_pages['multipass-settings']['tabs']['hoteldruid'] = 'HotelDruid';

		return $settings_pages;
	}

	function register_settings_fields( $meta_boxes ) {
		$prefix = 'hoteldruid_';

		$meta_boxes['multipass'] = array(
			'title'          => __( 'MultiPass Import', 'hoteldruid-migration' ),
			'id'             => 'multipass-hoteldruid-settings',
			'settings_pages' => array( 'hoteldruid-migration' ),
			// 'tab'            => 'hoteldruid',
			'fields'         => array(
				// array(
				// 	'name'     => __( 'Data found in backup file', 'hoteldruid-migration' ),
				// 	'type'     => 'custom_html',
				// 	'callback' => array( $this, 'render_import_data' ),
				// ),
				array(
					'name'              => __( 'Resources', 'multipass' ),
					'id'                => 'resources',
					'type'              => 'group',
					'fields'            => $this->resource_group_fields(),
					'sanitize_callback' => array( $this, 'sanitize_resource' ),
				),
				array(
					'name'              => __( 'Import now', 'multipass' ),
					'id'                => $prefix . 'import_now',
					'type'              => 'switch',
					'desc'              => __( 'Import bookings in MultiPass, create missing prestations, only proceed once. HotelDruid Migration module can be safely deactivated and uninstalled after importation.', 'multipass' ),
					'style'             => 'rounded',
					'sanitize_callback' => array( $this, 'import_now_sanitize_callback' ),
					'save_field'        => false,
				),
			),
		);

		return $meta_boxes;
	}

	function register_fields( $meta_boxes ) {
		// HotelDruid settings

		$prefix = 'hoteldruid_';

		$wc_term    = get_term_by( 'slug', 'hoteldruid', 'mltp_detail-source' );
		$wc_term_id = ( $wc_term ) ? get_term_by( 'slug', 'hoteldruid', 'mltp_detail-source' )->term_id : 'hoteldruid';
		// Order info on mltp_detail

		// Prestation info on WC Orders
		$prefix = 'prestation_';

		$meta_boxes['resources']['fields'][] = array(
			'name'          => __( 'HotelDruid', 'multipass' ),
			'id'            => 'resource_hoteldruid_id',
			'type'          => 'select_advanced',
			'options'       => $this->get_appartementi_options(),
			'placeholder'   => __( 'Select a lodging', 'multipass' ),
			'admin_columns' => array(
				'position'   => 'before date',
				'sort'       => true,
				'searchable' => true,
			),
			'columns'       => 3,
		);

		return $meta_boxes;
	}

	function get_appartementi_options() {
		$options = array();
		foreach ( $this->appartementi as $key => $value ) {
			$options[ $key ] = $key;
		}
		return $options;
	}

	function resource_group_fields() {
		$fields = array();
		foreach ( $this->appartementi as $key => $value ) {
			$fields[] = array(
				'name'        => $key,
				'id'          => $key,
				'type'        => 'post',
				'post_type'   => array( 'mltp_resource' ),
				'field_type'  => 'select_advanced',
				'placeholder' => __( 'Do not import', 'multipass' ),
				'size'        => 5,
			);
		}
		return $fields;
	}

	function sanitize_resource( $values, $field, $old_value ) {
		// $options = ;
		$options = array_fill_keys( array_keys( $this->get_appartementi_options() ), null );
		$options = array_replace( $options, $values );
		// error_log("options " . print_r($options, true));

		foreach ( $options as $idappartamenti => $resource_id ) {
			$query = $this->query_resources( $idappartamenti );

			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id = get_the_ID();
				if ( $post_id !== $resource_id ) {
					update_post_meta( $post_id, 'resource_hoteldruid_id', null );
				}
			}

			if ( ! empty( $resource_id ) ) {
				// add reference to resource
				$resource = new Mltp_Resource( $resource_id );
				if ( $resource ) {
					update_post_meta( $resource->id, 'resource_hoteldruid_id', $idappartamenti );
				} else {
					$resource_id = null;
				}
				// error_log("$idappartamenti = $resource_id resource->id $resource->id " . print_r($meta, true));
			}
		}

		return $values;
	}

	function query_resources( $idappartamenti = null ) {
		$args  = array(
			'posts_per_page' => -1,
			'post_type'      => 'mltp_resource',
			'meta_query'     => array(
				array(
					'meta_key' => 'resource_hoteldruid_id',
					'value'    => $idappartamenti,
				),
			),
		);
		$query = new WP_Query( $args );
		return $query;
	}

	function get_resource_id( $idappartamenti = null ) {
		if ( empty( $idappartamenti ) ) {
			return;
		}
		$query = $this->query_resources( $idappartamenti );
		if ( $query->have_posts() ) {
			$query->the_post();
			return get_the_ID();
		}
	}

	function render_import_data() {
		$html = sprintf(
			'%s appartments<br/>
			%s clients<br/>
			%s bookings',
			count( $this->appartementi ),
			count( $this->clienti ),
			count( $this->prenota ),
		);
		return $html;
	}

	function register_sources_filter( $sources ) {
		$sources['hoteldruid'] = 'HotelDruid';
		return $sources;
	}

	function get_related_links( $post_id, $relation_id, $direction ) {
		if ( empty( $post_id ) || empty( $relation_id ) ) {
			return array();
		}
		$related = array();

		return $related;
	}

	function background_process() {
		$this->background_queue = new Mltp_HotelDruid_Process();

		// $action = __CLASS__ . '::fetch_mails';
		// if(get_transient('Mltp_HotelDruid_wait')) return;
		// set_transient('Mltp_HotelDruid_wait', true, 30);
		//
		// if(MultiPass::get_option('email_processing', false))
		// $this->background_queue->push_to_queue(__CLASS__ . '::fetch_mails');
		//
		// $this->background_queue->save()->dispatch();

		// One-off task:
		//
		// $this->background_request = new Mltp_HotelDruid_Request();
		// $this->background_request->data( array( 'value1' => $value1, 'value2' => $value2 ) );
		// $this->background_request->dispatch();
	}

	function update_resource_title( $data ) {
		if ( empty( $_REQUEST['resource_hoteldruid_id'] ) ) {
			return $data;
		}

		if ( empty( $data['post_title'] ) ) {
			$data['post_title'] = get_the_title( $_REQUEST['resource_hoteldruid_id'] );
			$data['post_name']  = sanitize_title( $data['post_title'] );
		}

		return $data;
	}

	function import_now_sanitize_callback( $value, $field, $oldvalue ) {
		if ( $value == true ) {
			self::import_now();
		}

		return false; // sync_order field should never be saved
	}

	function import_now() {
		ini_set('max_execution_time', 300);
		foreach ( $this->appartementi as $idappartamenti => $appartment ) {
			$appartment['resource_id'] = $this->get_resource_id( $idappartamenti );
			if ( empty( $appartment['resource_id'] ) ) {
				continue;
			}

			$appartments[ $idappartamenti ] = $appartment;
		}

		$bookings = $this->prenota;
		if ( empty( $bookings ) ) {
			return false;
		}

		$clients = $this->clienti;
		$i=0;
		foreach ( $bookings as $key => $prenota ) {
			$booking = $prenota;
			$client          = $clients[ $booking['idclienti'] ];

			$resource_id = Mltp_Resource::get_resource_id( 'hoteldruid', $booking['idappartamenti'] );
			if ( ! $resource_id ) {
				// No resource associated with this property.
				continue;
			}

			$resource = new Mltp_Resource( $resource_id );
			$debug    = '';

			// $status = $booking['status'];
			// if(in_array($status, [ 'Declined', 'Open', 'Unavailable' ] )) continue;

			// if( ! in_array( $status, [ 'Booked', 'Tentative' ] ) ) {
			// error_log(
			// "Check status "
			// . print_r( $booking, true )
			// );
			// break;
			// }
			// $confirmed = (in_array($status, [ 'Booked' ])) ? true : false;
			$confirmed = true;

			$created    = strtotime( $booking['datainserimento'] );
			$modified   = strtotime( $booking['data_modifica'] );
			$from       = strtotime( $booking['arrival'] );
			$to         = strtotime( $booking['departure'] );
			$dates      = array(
				'from' => $from,
				'to'   => $to,
			);
			$date_range = MultiPass::format_date_range( $dates );


			$prestation_args = array(
				'customer_name'  => $client['displayname'],
				'customer_email' => preg_replace( '/,.*/', '', $client['email'] ),
				'customer_phone' => preg_replace( '/,.*/', '', $client['phone'] ),
				'date'           => MultiPass::format_date_iso( $created ),
				// 'source_url'       => $source_url,
				// 'origin_url'       => $origin_url,
				// 'confirmed' => $confirmed,
				'from'           => $from,
				'to'             => $to,
			);

			$prestation = new Mltp_Prestation( $prestation_args, true );

			if ( ! $prestation ) {
				error_log( __CLASS__ . '::' . __FUNCTION__ . ' Could not find nor create prestation, aborting import' );
				return false;
			}
			$edit_url  = get_edit_post_link( $prestation->ID );
			$guests_total = $booking['num_persone'];

			if( preg_match('/(adult)/', $booking['cat_persone']) ) {
				$booking['adults'] = preg_replace('/1([0-9]+)>s.*adult.*/', '$1', $booking['cat_persone']);
				$booking['children'] = ( preg_match('/(child|enfant)/', $booking['cat_persone']) ) ? preg_replace('/.*adult.*([0-9]+)>s>.*/', '$1', $booking['cat_persone']) : 0;
			} else if( preg_match('/(child|enfant)/', $booking['cat_persone']) ) {
				$booking['adults'] = 0;
				$booking['children'] = preg_replace('/1([0-9]+)>s.*/', '$1', $booking['cat_persone']);
			}

			$guests_adults    = empty($booking['adults']) ? 0 : $booking['adults'];
			$guests_children  = empty($booking['children']) ? 0 : $booking['children'];

			$source_url = null;

			$p_replace  = array(
				'/AirbnbIntegration/' => 'airbnb',
				'/Booking.?Com/i'     => 'booking',
				'/Manual/'            => 'hoteldruid',
			);
			$origin     = sanitize_title( preg_replace( array_keys( $p_replace ), $p_replace, $booking['origine'] ) );
			$origin_id = null; // $booking['hostinserimento']
			$origin_url = null; // $booking['hostinserimento']
			switch ( $origin ) {
				case 'airbnb':
					break;

				case 'booking':
					$idexpr         = '/.*Booking.com ([0-9]+) - .*/i';
					$origin_details = preg_match( $idexpr, $booking['commento'] ) ? $booking['commento'] : null;
					$origin_details = str_replace( array( "\r", "\n", "\t" ), ' ', $origin_details );
					$origin_id      = preg_replace( $idexpr, '$1', $origin_details );
					$checkin_time   = ( preg_match( '/.*arrival: between ([0-9:]+)[[:blank:]].*/i', $origin_details ) )
					? preg_replace( '/.*arrival: between ([0-9:]+)[[:blank:]].*/i', '$1', $origin_details ) : null;
					// $origin_url     = ( empty( $origin_id ) ) ? null : 'https://admin.booking.com/hotel/hoteladmin/extranet_ng/manage/booking.html?res_id=' . $origin_id;
					break;

				case 'expedia':
					$idexpr         = '/.*Expedia ([0-9]+)([^0-9].*)$/i';
					$origin_details = preg_match( $idexpr, $booking['commento'] ) ? $booking['commento'] : null;
					$origin_details = str_replace( array( "\r", "\n", "\t" ), ' ', $origin_details );
					$origin_id      = preg_replace( $idexpr, '$1', $origin_details );
					// $origin_url     = ( empty( $origin_id ) ) ? null : 'https://apps.expediapartnercentral.com/lodging/bookings?bookingItemId=' . $origin_id;
					break;

				// default:
			}
			$origin_url     = MultiPass::origin_url($origin, $origin_id);

			$description = "$resource->name, ${guests_total}p $date_range";

			$item_args = array(
				'source'             => 'hoteldruid',
				'hoteldruid_uuid' => join('-', [ $booking['related'], $booking['idprenota'] ]),
				'source_id'          => $booking['related'],
				'source_item_id'     => $booking['idprenota'],
				// 'source_url'       => $source_url,
				'origin'             => $origin,
				'origin_url'         => $origin_url,
				'edit_url'           => $edit_url,
				// 'view_url'       => get_edit_post_link($prestation->ID),
				'date'           => MultiPass::format_date_iso( $created ),

				'resource_id'        => $resource_id,
				// 'status' => $status,
				'confirmed'          => ( 'S' === $booking['conferma'] ) ? true : null,
				'description'        => $description,
				'source_details'     => array(
					// 'rooms' => $booking['rooms'],
					'language' => $client['lingua'],
					'created'  => strtotime( $booking['datainserimento'] ),
					'updated'  => strtotime( $booking['data_modifica'] ),
					// 'canceled' => null,
					// 'is_deleted' => null,
				),
				'prestation_id'      => $prestation->ID,
				'customer'           => array(
					// TODO: try to get WP user if exists
					// 'user_id' => $customer_id,
					'name'  => $prestation_args['customer_name'],
					'email' => $prestation_args['customer_email'],
					'phone' => $prestation_args['customer_phone'],
				),
				'dates'              => $dates,
				'from' => $from,
				'to' => $to,
				'attendees'          => array(
					'total' => $guests_total,
					'adults'   => $guests_adults,
					'children' => $guests_children,
				),
				// // 'beds' => $beds,
				'price'              => array(
					// 'quantity'  => 1,
					'unit'      => $booking['tariffa_tot'] + $booking['sconto'],
					'sub_total' => $booking['tariffa_tot'] + $booking['sconto'],
				),
				'discount'           => $booking['sconto'],
				'total'              => $booking['tariffa_tot'],
				'deposit'            => $booking['caparra'],
				'paid'               => $booking['pagato'],
				'balance'            => $booking['tariffa_tot'] - $booking['pagato'],
				'commission'         => $booking['commissioni'],
				'taxes'              => $booking['tasseperc'],
				'type'               => 'booking',
				'notes'              => $booking['commento'],
				'hoteldruid_data' => $prenota,
			);

			$prestation_item = new Mltp_Item( $item_args, true );
			$prestation->update();

			error_log(
				__CLASS__ . " Imported $booking[related] $prestation_args[customer_name] - $description - paid $item_args[paid]/$item_args[total] balance $item_args[balance]"
				// . "\nbooking details " . print_r($booking, true)
				// . "\nclient " . print_r($client, true)
				// . "\nprestation " . print_r($prestation_args, true)
				// . "\nprestation $prestation->ID $prestation->name"
				// . "\ndetails " . print_r($item_args, true)
			);

		}

		// if(isset($_REQUEST['hoteldruid_sync_now']) && $_REQUEST['hoteldruid_sync_now'] == true) {
		// $orders = wc_get_orders( array(
		// 'limit'        => -1, // Query all orders
		// 'orderby'      => 'date',
		// 'order'        => 'ASC',
		// 'meta_key'     => 'prestation_id', // The postmeta key field
		// 'meta_compare' => 'NOT EXISTS', // The comparison argument
		// ));
		// error_log("found " . count($orders) . " order(s) without prestation");
		// foreach ($orders as $key => $order) {
		// $order_post = get_post($order->get_id());
		// self::update_order_prestation($order_post->ID, $order_post, true);
		// }
		// }
	}

}

$modules[] = new Mltp_HotelDruid();
