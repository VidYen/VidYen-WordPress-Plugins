<?php

//normal boot out stuff
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * exit uninstall if not called by WP
 */
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

//Usual call to get the database up
global $wpdb;

//Only need to drop the battles, and battles logs. Items don't seem to be problematic.

$table_name_pending_battles = $wpdb->prefix . 'vypsg_pending_battles';
$table_name_battles = $wpdb->prefix . 'vypsg_battles';
//$table_name_tracking = $wpdb->prefix . 'vypsg_tracking'; //I don't think this actually needs to be dropped, but it seems to have more meta than it should. It's violating my log vision though.

// drop the table from the database. NOTE: I called each without a loop as this should be very simple and specific. -Felty
$wpdb->query( "DROP TABLE IF EXISTS $table_name_pending_battles" );
$wpdb->query( "DROP TABLE IF EXISTS $table_name_battles" );

// drop the table from the database. NOTE: Again I called each without a loop as this should be very simple and specific.
// Also I feel like I should just call the variable what they are so can be visually followed rather than traced.  -Felty

//$wpdb->query( "DROP TABLE IF EXISTS $table_name_tracking" );
