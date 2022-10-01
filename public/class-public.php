<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    Hoteldruid_Migration
 * @subpackage Hoteldruid_Migration/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Hoteldruid_Migration
 * @subpackage Hoteldruid_Migration/public
 * @author     Your Name <email@example.com>
 */
class Hoteldruid_Migration_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $hoteldruid_migration    The ID of this plugin.
	 */
	private $hoteldruid_migration;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 * @param      string    $hoteldruid_migration       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $hoteldruid_migration, $version ) {

		$this->hoteldruid_migration = $hoteldruid_migration;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the init() function
		 * defined in Hoteldruid_Migration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Hoteldruid_Migration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->hoteldruid_migration, plugin_dir_url( __FILE__ ) . 'css/public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the init() function
		 * defined in Hoteldruid_Migration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Hoteldruid_Migration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->hoteldruid_migration, plugin_dir_url( __FILE__ ) . 'js/public.js', array( 'jquery' ), $this->version, false );

	}

}
