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
 *
 * Icon1x: https://github.com/magicoli/hoteldruid-migration/raw/master/assets/icon-128x128.jpg
 * Icon2x: https://github.com/magicoli/hoteldruid-migration/raw/master/assets/icon-256x256.jpg
 * BannerHigh: https://github.com/magicoli/hoteldruid-migration/raw/master/assets/banner-1544x500.jpg
 * BannerLow: https://github.com/magicoli/hoteldruid-migration/raw/master/assets/banner-772x250.jpg
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
define( 'HOTELDRUID_MIGRATION_VERSION', '0.1.0' );
define( 'HOTELDRUID_MIGRATION_PLUGIN_NAME', 'hoteldruid-migration' );

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
	$plugin->init();

}
run_hoteldruid_migration();
