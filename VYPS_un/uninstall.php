<?php
/* Due to problems with data loss, this plugin is designed to clean the
 * the main tables for the VYPS_base which are vyps_points and vyps_point_log
 * Removing Plugin data using uninstall.php
 * the below function clears the database table on uninstall
 * only loads this file when uninstalling a plugin.
 *
 */

/* 
 * exit uninstall if not called by WP
 */
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

/* 
 * Making WPDB as global
 * to access database information.
 */
global $wpdb;

$table_name_points = $wpdb->prefix . 'vyps_points';

// drop the table from the database. NOTE: I called each without a loop as this should be very simple and specific. -Felty

$wpdb->query( "DROP TABLE IF EXISTS $table_name_points" );

$table_name_log = $wpdb->prefix . 'vyps_points_log';

// drop the table from the database. NOTE: Again I called each without a loop as this should be very simple and specific.
// Also I feel like I should just call the variable what they are so can be visually followed rather than traced.  -Felty

$wpdb->query( "DROP TABLE IF EXISTS $table_name_log" );
