<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://avaysi.ir
 * @since      1.0.0
 *
 * @package    Baran
 * @subpackage Baran/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Baran
 * @subpackage Baran/includes
 * @author     ابوالفضل ویسی <vaysi.erfan@gmail.com>
 */
class Baran_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        global $table_prefix,$wpdb;
        $wp_track_table = $table_prefix . "baran_logs";
        $wpdb->query( "DROP TABLE IF EXISTS " . $wp_track_table );
        wp_clear_scheduled_hook( 'runAllCronJobs' );
	}

}
