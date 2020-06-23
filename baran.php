<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://avaysi.ir
 * @since             1.0.0
 * @package           Baran
 *
 * @wordpress-plugin
 * Plugin Name:       حسابداری باران
 * Plugin URI:        http://avaysi.ir
 * Description:       افزونه اتصال ووکامرس به نرم افزار حسابداری باران
 * Version:           1.0.0
 * Author:            ابوالفضل ویسی
 * Author URI:        http://avaysi.ir
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       baran
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BARAN_VERSION', '1.0.0' );
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

define('BARAN_PATH',plugin_dir_path(__FILE__));
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-baran-activator.php
 */
function activate_baran() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-baran-activator.php';
	Baran_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-baran-deactivator.php
 */
function deactivate_baran() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-baran-deactivator.php';
	Baran_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_baran' );
register_deactivation_hook( __FILE__, 'deactivate_baran' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/helpers.php';

require plugin_dir_path( __FILE__ ) . 'includes/class-baran.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_baran() {

	$plugin = new Baran();
	$plugin->run();


}
run_baran();

add_action('init','myCron');

add_action('runAllCronJobs','syncToBaran');