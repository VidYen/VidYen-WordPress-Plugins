<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//This is a complete new user log. I'm going to do advance SQL stuff as much as I beat on the old version, it wouldn't work the way I wanted.

/* Main Public Log shortcode function */

function vyps_user_log_func($atts)
{

	//Shortcode stuff
	//I'm going to eventually have site admins set logs for activities like reason etc and the meta fields, but for now.
	$atts = shortcode_atts(
		array(
				'point_id' => 0,
				'reason' => '',
				'rows' => 50,
				'bootstrap' => 'no',,
				'pages' => 10, //How many pages will have
		), $atts, 'vidyen-user-log' );

	$point_id = $atts['point_id'];
	$reason = $atts['reason'];
	$table_row_limit = $atts['rows']; //50 by default
	$boostrap_on = $atts['bootstrap'];
	$max_pages = $atts['pages'];
	$max_pages_middle = intval($max_pages/2); //The middle in theory. I guess?

	//This is obvious
	$user_id = get_current_user_id(); //Over riding the current userid to show just the current user. I have no idea if this actually works as may have not set it up correctly.

	//SQL setup stuff
	global $wpdb;
	$table_name_points = $wpdb->prefix . 'vyps_points';
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_users = $wpdb->prefix . 'users'; //Needed for their name.

	//SQL query of current user
	$user_data_row_query = "SELECT * FROM ". $table_name_log . " WHERE id = %d";
	$user_data_query_prepared = $wpdb->prepare( $user_data_query, $user_id );
	$user_data = $wpdb->get_results( $user_data_query_prepared );

	print_r($user_data); //Guessing this is an array.
}
