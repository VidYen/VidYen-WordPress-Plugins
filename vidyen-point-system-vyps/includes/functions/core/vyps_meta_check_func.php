<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/***   This function is just to see if the metaid exists   ***/
/*** Due to the terrible nature of post back system you have to do this ***/

//It would be one of my to do's if i had a time machine...
//To go back and berrate the inventor of the post back system and tell them its crap, always will be, and they they should use authenticated json systems
//It's sad, when coin hive has a better system


function vyps_meta_check_func( $atts )
{

	//NOTE: If there are shortcodes they go in, but generally it will be passed.

	$atts = shortcode_atts(
		array(
				'btn_name' => '',
    ), $atts, 'vyps-pe' );

	//NOTE: Only adding. So do not need any input variables
	//ALL THESE SHOULD BE INTS as VYPS is INT only!
	$meta_id = sanitize_text_field($atts['btn_name']); //Should already be passed and sanitized, but I feel double santization never hurt.

	//$WPDB calls
	global $wpdb;
	$table_name_log = $wpdb->prefix . 'vyps_points_log'; //Don't need the points, just log.

	//Query
	$meta_id_query = "SELECT count(vyps_meta_id) FROM ". $table_name_log . " WHERE vyps_meta_id = %s"; //I'm not sure if this is resource optimal but it works. -Felty
	$meta_id_query_prepared = $wpdb->prepare( $meta_id_query, $meta_id );
	$meta_id_count = $wpdb->get_var( $meta_id_query_prepared );

	if ($meta_id_count > 0) //So if it exists it will be > 0, which means the transaction already exists.
	{

		return '2'; //This means confirmed duplicate... If 2 then it is

	}

	elseif ($meta_id_count == 0)
	{

	//This means that there are no counts which means we are fine to proceed.
	return 1;

	}

	else
	{
		return 0; //Yes, implicit 0... for human reading... This means that something has gone horribly wrong. Possible sql error.
	}

}
