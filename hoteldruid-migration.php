<?php
/**
 * @wordpress-plugin
 * Plugin Name:       HotelDruid migration tool to WooCommerce
 * Plugin URI:        https://magiiic.com/wordpress/hoteldruid-migration
 * Description:       Migrate HotelDruid backup to WooCommerce Bookings
 * Version:           0.1.0
 * Author:            Magiiic
 * Author URI:        https://magiiic.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       hoteldruid-migration
 * Domain Path:       /languages
 *
 * @link              https://magiiic.com/wordpress/hoteldruid-migration
 * @since             0.1.0
 * @package           Hoteldruid_Migration
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 0.1.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'HMIGR_MIGRATION_VERSION', '0.1.0' );
define( 'HMIGR_PLUGIN_NAME', 'hoteldruid-migration' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-activator.php
 */
function activate_hoteldruid_migration() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-activator.php';
	Hoteldruid_Migration_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-deactivator.php
 */
function deactivate_hoteldruid_migration() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-deactivator.php';
	Hoteldruid_Migration_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_hoteldruid_migration' );
register_deactivation_hook( __FILE__, 'deactivate_hoteldruid_migration' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
require plugin_dir_path( __FILE__ ) . 'includes/class.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_hoteldruid_migration() {

	$plugin = new Hoteldruid_Migration();
	$plugin->run();

}
run_hoteldruid_migration();
