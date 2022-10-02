<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    Hoteldruid_Migration
 * @subpackage Hoteldruid_Migration/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.1.0
 * @package    Hoteldruid_Migration
 * @subpackage Hoteldruid_Migration/includes
 * @author     Your Name <email@example.com>
 */
class Hoteldruid_Migration {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      Hoteldruid_Migration_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $hoteldruid_migration    The string used to uniquely identify this plugin.
	 */
	protected $hoteldruid_migration;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function __construct() {
		if ( defined( 'HOTELDRUID_MIGRATION_VERSION' ) ) {
			$this->version = HOTELDRUID_MIGRATION_VERSION;
		} else {
			$this->version = '0.1.0';
		}
		if ( defined( 'HOTELDRUID_MIGRATION_PLUGIN_NAME' ) ) {
			$this->hoteldruid_migration = HOTELDRUID_MIGRATION_PLUGIN_NAME;
		} else {
			$this->hoteldruid_migration = 'hoteldruid-migration';
		}

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Hoteldruid_Migration_Loader. Orchestrates the hooks of the plugin.
	 * - Hoteldruid_Migration_i18n. Defines internationalization functionality.
	 * - Hoteldruid_Migration_Admin. Defines all hooks for the admin area.
	 * - Hoteldruid_Migration_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/metabox.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-public.php';

		add_filter('multipass_load_modules', function($modules) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/modules/class-mltp-hoteldruid.php';
			return $modules;
		});

		$this->loader = new Hoteldruid_Migration_Loader();

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-settings.php';
		$this->settings = new Hoteldruid_Migration_Settings();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Hoteldruid_Migration_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Hoteldruid_Migration_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Hoteldruid_Migration_Admin( $this->get_hoteldruid_migration(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Hoteldruid_Migration_Public( $this->get_hoteldruid_migration(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.1.0
	 */
	public function init() {
		$this->loader->init();
		$this->settings->init();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.1.0
	 * @return    string    The name of the plugin.
	 */
	public function get_hoteldruid_migration() {
		return $this->hoteldruid_migration;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.1.0
	 * @return    Hoteldruid_Migration_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
