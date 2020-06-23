<?php

/**
 * Fired during plugin activation
 *
 * @link       http://avaysi.ir
 * @since      1.0.0
 *
 * @package    Baran
 * @subpackage Baran/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Baran
 * @subpackage Baran/includes
 * @author     ابوالفضل ویسی <vaysi.erfan@gmail.com>
 */
class Baran_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        global $table_prefix, $wpdb;

        $tblname = 'baran_logs';
        $wp_track_table = $table_prefix . $tblname;

        #Check to see if the table exists already, if not, then create it

        if($wpdb->get_var( "show tables like '$wp_track_table'" ) != $wp_track_table)
        {
            $sql = "CREATE TABLE `". $wp_track_table . "` ( ";
            $sql .= "  `id`  int(11)   NOT NULL auto_increment, ";
            $sql .= "  `createdAt`  int(128)   NOT NULL, ";
            $sql .= " `success` BOOLEAN NOT NULL DEFAULT FALSE,";
            $sql .= "  PRIMARY KEY (`id`) ";
            $sql .= ") AUTO_INCREMENT=1 ; ";
            require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
            dbDelta($sql);
        }

        if(get_option('minCount')){
            update_option('minCount',1);
        }else {
            add_option('minCount',1);
        }
	}

}
