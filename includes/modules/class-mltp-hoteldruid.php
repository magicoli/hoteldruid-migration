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
				'hook'     => 'multipass_register_terms_prestation-item-source',
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

		$meta_boxes['multipass-hoteldruid-settings'] = array(
			'title'          => __( 'HotelDruid Settings', 'hoteldruid-migration' ),
			'id'             => 'multipass-hoteldruid-settings',
			'settings_pages' => array( 'multipass-settings' ),
			'tab'            => 'hoteldruid',
			'fields'         => array(
				array(
					'name'  => __( 'Disclaimer', 'hoteldruid-migration' ),
					'type'  => 'custom_html',
					'desc'  => HOTELDRUID_DISCLAIMER,
					'class' => 'warning',
				),
				array(
					'name'     => __( 'Data found in backup file', 'hoteldruid-migration' ),
					'type'     => 'custom_html',
					'callback' => array( $this, 'render_import_data' ),
				),
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

		$meta_boxes['multipass-settings']['fields']['currency_options'] = array(
			'name' => __( 'Currency Options', 'multipass' ),
			'id'   => $prefix . 'currency',
			'type' => 'custom_html',
			'std'  => sprintf(
				__( 'Set currency options in %1$sHotelDruid settings page%2$s', 'multipass' ),
				'<a href="' . get_admin_url( null, 'admin.php?page=wc-settings#pricing_options-description' ) . '">',
				'</a>',
			),
		);

		$wc_term    = get_term_by( 'slug', 'hoteldruid', 'prestation-item-source' );
		$wc_term_id = ( $wc_term ) ? get_term_by( 'slug', 'hoteldruid', 'prestation-item-source' )->term_id : 'hoteldruid';
		// Order info on prestation-item

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
				'post_type'   => array( 'mp_resource' ),
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

		foreach ( $options as $idappartementi => $resource_id ) {
			$query = $this->get_resources( $idappartementi );

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
					update_post_meta( $resource->id, 'resource_hoteldruid_id', $idappartementi );
				} else {
					$resource_id = null;
				}
				// error_log("$idappartementi = $resource_id resource->id $resource->id " . print_r($meta, true));
			}
		}

		return $values;
	}

	function get_resources( $idappartementi = null ) {
		$args  = array(
			'posts_per_page' => -1,
			'post_type'      => 'mp_resource',
			'meta_query'     => array(
				array(
					'meta_key' => 'resource_hoteldruid_id',
					'value'    => $idappartementi,
				),
			),
		);
		$query = new WP_Query( $args );
		return $query;
	}

	function get_resource_id( $idappartementi = null ) {
		if ( empty( $idappartementi ) ) {
			return;
		}
		$query = $this->get_resources( $idappartementi );
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

	function save_post_action( $post_id, $post, $update ) {
		if ( ! $update ) {
			return;
		}
		if ( 'shop_order' !== $post->post_type ) {
			return;
		}

		remove_action( current_action(), __CLASS__ . '::' . __FUNCTION__ );

		self::update_order_prestation( $post_id, $post, $update );

		add_action( current_action(), __CLASS__ . '::' . __FUNCTION__, 10, 3 );
	}

	function wp_insert_post_action( $post_id, $post, $update ) {
		if ( ! $update ) {
			return;
		}
		if ( MultiPass::is_new_post() ) {
			return; // new posts are empty
		}

		remove_action( current_action(), __CLASS__ . '::' . __FUNCTION__ );
		switch ( $post->post_type ) {
			// case 'shop_order':
			// self::update_order_prestation($post_id, $post, $update );
			// break;

			case 'prestation':
				self::update_prestation_orders( $post_id, $post, $update );
				break;
		}
		add_action( current_action(), __CLASS__ . '::' . __FUNCTION__, 10, 3 );
	}

	function import_now_sanitize_callback( $value, $field, $oldvalue ) {
		if ( $value == true ) {
			self::import_now();
		}

		return false; // sync_order field should never be saved
	}

	function import_now() {
		$orders = wc_get_orders(
			array(
				'limit'   => -1, // Query all orders
				'orderby' => 'date',
				'order'   => 'ASC',
			// 'meta_key'     => 'prestation_id', // The postmeta key field
			// 'meta_compare' => 'NOT EXISTS', // The comparison argument
			)
		);
		foreach ( $orders as $key => $order ) {
			$order_post = get_post( $order->get_id() );
			self::update_order_prestation( $order_post->ID, $order_post, true );
		}
	}

}

$modules[] = new Mltp_HotelDruid();
