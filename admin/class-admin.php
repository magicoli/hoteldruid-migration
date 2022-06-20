<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    Hoteldruid_Migration
 * @subpackage Hoteldruid_Migration/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Hoteldruid_Migration
 * @subpackage Hoteldruid_Migration/admin
 * @author     Your Name <email@example.com>
 */
class Hoteldruid_Migration_Admin {

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
	 * @param      string    $hoteldruid_migration       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $hoteldruid_migration, $version ) {

		$this->hoteldruid_migration = $hoteldruid_migration;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Hoteldruid_Migration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Hoteldruid_Migration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->hoteldruid_migration, plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Hoteldruid_Migration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Hoteldruid_Migration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->hoteldruid_migration, plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), $this->version, false );

	}

}

function hdm_admin_notice($notice, $class='info', $dismissible=true ) {
  if(empty($notice)) return;
  if($dismissible) $is_dismissible = 'is-dismissible';
	if(is_admin()) {
  add_action( 'admin_notices', function() use ($notice, $class, $is_dismissible) {
    ?>
    <div class="notice notice-<?=$class?> <?=$is_dismissible?>">
        <p><strong><?php echo HMIGR_PLUGIN_NAME; ?></strong>: <?php _e( $notice, 'band-tools' ); ?></p>
    </div>
    <?php
  } );
	} else {
		hdm_transient_admin_notice($notice, $class, $dismissible, __FUNCTION__);
	}
}

function hdm_transient_admin_notice( $notice, $class='info', $dismissible=true, $key = NULL ) {
	$transient_key = sanitize_title(HMIGR_PLUGIN_NAME . '_hdm_transient_admin_notices');

	$queue = get_transient( $transient_key );
	if(!is_array($queue)) $queue = array($queue);

	$hash = hash('md5', $notice);
	$queue[$hash] = array('notice' => $notice, 'class' => $class, 'dismissible' => $dismissible);
	set_transient( $transient_key, $queue );
}

function hdm_get_transient_admin_notices() {
	if(!is_admin()) return;
	$transient_key = sanitize_title(HMIGR_PLUGIN_NAME . '_hdm_transient_admin_notices');
	$queue = get_transient( $transient_key );
	if(!is_array($queue)) $queue = array($queue);
	foreach($queue as $key => $notice) {
		if(!is_array($notice)) continue;
		hdm_admin_notice($notice['notice'], $notice['class'], $notice['dismissible'] );
	}
	delete_transient( $transient_key );
}
add_action('admin_head', 'hdm_get_transient_admin_notices');
