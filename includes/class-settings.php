<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Hoteldruid_Migration
 * @subpackage Hoteldruid_Migration/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Hoteldruid_Migration
 * @subpackage Hoteldruid_Migration/includes
 * @author     Your Name <email@example.com>
 */
class Hoteldruid_Migration_Settings {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->actions = array();
		$this->filters = array();

		if ( is_plugin_active( 'multipass/multipass.php' ) ) {
			$parent_menu = 'multipass';
			$parent_slug = 'admin.php';
		} else {
			$parent_menu = 'tools.php';
			// $parent_slug = 'tools.php';
		}

		define( 'HOTELDRUID_MIGRATION_MENU', $parent_menu );
		// define( 'HOTELDRUID_MIGRATION_PARENT', $parent_slug );
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string $hook             The name of the WordPress action that is being registered.
	 * @param    object $component        A reference to the instance of the object on which the action is defined.
	 * @param    string $callback         The name of the function definition on the $component.
	 * @param    int    $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int    $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string $hook             The name of the WordPress filter that is being registered.
	 * @param    object $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string $callback         The name of the function definition on the $component.
	 * @param    int    $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int    $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array  $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string $hook             The name of the WordPress filter that is being registered.
	 * @param    object $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string $callback         The name of the function definition on the $component.
	 * @param    int    $priority         The priority at which the function should be fired.
	 * @param    int    $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;

	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function init() {

		$actions = array(

		);

		$filters = array(
			array(
				'hook'     => 'mb_settings_pages',
				'callback' => 'register_settings_pages',
				'priority' => 30,
			),
			array(
				'hook'     => 'rwmb_meta_boxes',
				'callback' => 'register_settings_fields',
			),
			array(
				'hook'     => 'plugin_action_links_hoteldruid-migration/hoteldruid-migration.php',
				'callback' => 'plugin_action_links',
			),
			array(
				'hook'     => 'rwmb_meta_boxes',
				'callback' => 'hoteldruid_migration_product_hoteldruid_link',
			),
		);

		if ( ! empty( hdm_get_option( 'import_data' ) ) ) {
			$filters = $filters + array(
				array(
					'hook'     => 'rwmb_meta_boxes',
					'callback' => 'hoteldruid_migration_tab_accommodations',
				),
				array(
					'hook'     => 'rwmb_meta_boxes',
					'callback' => 'hoteldruid_migration_tab_clients',
				),
				array(
					'hook'     => 'rwmb_meta_boxes',
					'callback' => 'hoteldruid_migration_tab_bookings',
				),
			);
		}

		foreach ( $filters as $hook ) {
			( empty( $hook['component'] ) ) && $hook['component']         = __CLASS__;
			( empty( $hook['priority'] ) ) && $hook['priority']           = 10;
			( empty( $hook['accepted_args'] ) ) && $hook['accepted_args'] = 1;
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $actions as $hook ) {
			( empty( $hook['component'] ) ) && $hook['component']         = __CLASS__;
			( empty( $hook['priority'] ) ) && $hook['priority']           = 10;
			( empty( $hook['accepted_args'] ) ) && $hook['accepted_args'] = 1;
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

	}

	static function register_settings_pages( $settings_pages ) {
		$settings_pages['hoteldruid-migration'] = array(
			'menu_title' => __( 'HotelDruid migration', 'hoteldruid-migration' ),
			'id'         => 'hoteldruid-migration',
			'position'   => 25,
			'parent'     => HOTELDRUID_MIGRATION_MENU,
			'capability' => 'manage_woocommerce',
			'style'      => 'no-boxes',
			'columns'    => 1,
			// 'tabs'       => [
			// 'settings'       => 'Settings',
			// 'accommodations' => 'Accomodations',
			// 'clients'        => 'Clients',
			// 'bookings'       => 'Bookings',
			// ],
			'icon_url'   => 'dashicons-admin-generic',
		);

		if ( ! empty( hdm_get_option( 'import_data' ) ) ) {
			$settings_pages['hoteldruid-migration']['tabs'] = array(
				'settings'       => __('Settings', 'hoteldruid-migration'),
				'accommodations' => 'Accomodations',
				'clients'        => 'Clients',
				'bookings'       => 'Bookings',
			);
		}

		return $settings_pages;
	}

	static function register_settings_fields( $meta_boxes ) {
		$prefix = '';

		$meta_boxes[] = array(
			'title'          => __( 'Settings', 'hoteldruid-migration' ),
			'id'             => 'hoteldruid-migration-settings',
			'settings_pages' => array( 'hoteldruid-migration' ),
			'tab'            => 'settings',
			'fields'         => array(
				array(
					'name' => __('Disclaimer', 'hoteldruid-migration'),
					'type' => 'custom_html',
					'desc' => HOTELDRUID_DISCLAIMER,
				),
				array(
					'name'              => __( 'HotelDruid backup file path', 'hoteldruid-migration' ),
					'id'                => $prefix . 'hoteldruid_backup_file',
					'type'              => 'file_input',
					'desc'              => __( 'HotelDruid backup file full path. Must be saved in a place readable by the web server, but outside website folder.', 'hoteldruid-migration' ),
					'placeholder'       => __( '/full/path/to/hoteld_backup.php', 'hoteldruid-migration' ),
					'columns'           => 9,
					'sanitize_callback' => 'hoteldruid_backup_file_validation',
				),
				array(
					'name'     => __( 'File info', 'hoteldruid-migration' ),
					'id'       => $prefix . 'file_info',
					'type'     => 'custom_html',
					'callback' => 'hdm_file_info_output',
					'visible'  => array(
						'when'     => array( array( 'hoteldruid_backup_file', '!=', '' ) ),
						'relation' => 'or',
					),
				),
				array(
					'name'              => __( 'Import data (modified)', 'hoteldruid-migration' ),
					'id'                => $prefix . 'import_data',
					'type'              => 'button_group',
					'options'           => hdm_import_button_values(),
					'sanitize_callback' => 'import_data_field_validation',
				),
				array(
					'name'              => __( 'Create users', 'multipass' ),
					'id'                => $prefix . 'create_users',
					'type'              => 'switch',
					'desc'              => __( 'Create WordPress users for HotelDruid clients if none exists.', 'multipass' ),
					'style'             => 'rounded',
					'visible'  => array(
						'when'     => array(
							array( 'hoteldruid_backup_file', '!=', '' ),
							array( 'import_data', '=', 'process' ),
						),
						'relation' => 'and',
					),
				),
			),
			'validation'     => array(
				'rules'    => array(
					$prefix . 'hoteldruid_backup_file' => array(
						'extension' => 'php,php.gz',
					),
				),
				'messages' => array(
					$prefix . 'hoteldruid_backup_file' => array(
						'extension' => 'Allowed formats: *.php or *.php.gz',
					),
				),
			),
		);

		return $meta_boxes;
	}

	static function hoteldruid_migration_tab_accommodations( $meta_boxes ) {
		$prefix = '';

		$meta_boxes[] = array(
			'title'          => __( 'Accommodations', 'hoteldruid-migration' ),
			'id'             => 'hoteldruid-migration-accommodations',
			'settings_pages' => array( 'hoteldruid-migration' ),
			'tab'            => 'accommodations',
			'fields'         => array(
				array(
					'id'       => $prefix . 'hdm_list_accommodations',
					'type'     => 'custom_html',
					'callback' => 'hdm_list_accommodations_output',
				),
			),
		);

		return $meta_boxes;
	}

	static function hoteldruid_migration_tab_clients( $meta_boxes ) {
		$prefix = '';

		$meta_boxes[] = array(
			'title'          => __( 'Clients', 'hoteldruid-migration' ),
			'id'             => 'hoteldruid-migration-clients',
			'settings_pages' => array( 'hoteldruid-migration' ),
			'tab'            => 'clients',
			'fields'         => array(
				array(
					'id'       => $prefix . 'hdm_list_clients',
					'type'     => 'custom_html',
					'callback' => 'hdm_list_clients_output',
				),
			),
		);

		return $meta_boxes;
	}

	static function hoteldruid_migration_tab_bookings( $meta_boxes ) {
		$prefix = '';

		$meta_boxes[] = array(
			'title'          => __( 'Bookings', 'hoteldruid-migration' ),
			'id'             => 'hoteldruid-migration-bookings',
			'settings_pages' => array( 'hoteldruid-migration' ),
			'tab'            => 'bookings',
			'fields'         => array(
				array(
					'id'       => $prefix . 'hdm_list_bookings',
					'type'     => 'custom_html',
					'callback' => 'hdm_list_bookings_output',
				),
			),
		);

		return $meta_boxes;
	}

	static function plugin_action_links( $links ) {

		$url   = esc_url(
			add_query_arg(
				array( 'page' => 'hoteldruid-migration' ),
				get_admin_url() . ( preg_match('/\.php$/', HOTELDRUID_MIGRATION_MENU ) ? HOTELDRUID_MIGRATION_MENU : 'admin.php' ),
			)
		);
		$links = array( 'settings' => "<a href='$url'>" . __( 'Settings', 'Hoteldruid_Migration' ) . '</a>' ) + $links;

		return $links;
	}

	static function hoteldruid_migration_product_hoteldruid_link( $meta_boxes ) {
		$prefix = '';

		$meta_boxes[] = array(
			'title'      => __( 'HotelDruid links', 'hoteldruid-migration' ),
			'id'         => 'product-hoteldruid-link',
			'post_types' => array( 'product' ),
			'context'    => 'side',
			'autosave'   => true,
			'include'    => array(
				'relation'     => 'OR',
				'product_type' => array( 125 ),
			),
			'fields'     => array(
				array(
					'name'    => __( 'HotelDruid idappartementi', 'hoteldruid-migration' ),
					'id'      => $prefix . 'hoteldruid_idappartementi',
					'type'    => 'select',
					'options' => hdm_get_idappartamenti_list(),
				),
			),
		);

		return $meta_boxes;
	}

}
