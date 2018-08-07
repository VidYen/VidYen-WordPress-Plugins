<?php
/*
 * Removing Plugin data using uninstall.php
 * the below function clears the database table on uninstall
 * only loads this file when uninstalling a plugin.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * exit uninstall if not called by WP
 */
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

//I commented everything out, but will install a proper uninstall page in 3.0

/*
 * Making WPDB as global
 * to access database information.
 */
//global $wpdb;

/*
 * @var $table_name
 * name of table to be dropped
 * prefixed with $wpdb->prefix from the database
 */
//$table_name_points = $wpdb->prefix . 'vyps_points';

// drop the table from the database. NOTE: I called each without a loop as this should be very simple and specific. -Felty

/* 6.8.2018 Commenting out the drop tables as need a better system so people do not have catastrophic data loss when they are
*  manually upgradding

$wpdb->query( "DROP TABLE IF EXISTS $table_name_points" );

*/

//$table_name_log = $wpdb->prefix . 'vyps_points_log';

// drop the table from the database. NOTE: Again I called each without a loop as this should be very simple and specific.
// Also I feel like I should just call the variable what they are so can be visually followed rather than traced.  -Felty

/* 6.8.2018 Commenting out the drop tables as need a better system so people do not have catastrophic data loss when they are
*  manually upgradding

$wpdb->query( "DROP TABLE IF EXISTS $table_name_log" );

*/
