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

//Adding this to remove the wm_setting table

/*
 * Making WPDB as global
 * to access database information.
 */
global $wpdb;

/*
 * @var $table_name
 * name of table to be dropped
 * prefixed with $wpdb->prefix from the database
 */

$vidyen_wm_settings = $wpdb->prefix . 'vidyen_wm_settings';

// drop the table from the database. NOTE: I called each without a loop as this should be very simple and specific. -Felty


//$wpdb->query( "DROP TABLE IF EXISTS $vidyen_wm_settings" );
