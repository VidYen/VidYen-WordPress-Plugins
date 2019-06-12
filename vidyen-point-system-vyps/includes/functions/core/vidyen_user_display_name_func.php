<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** USER DISPLAY NAME FUNCTION ***/
function vidyen_user_display_name($user_id)
{
  //The usual suspects to get the sql calls up
  global $wpdb;
	$table_name_users = $wpdb->prefix . 'users';

  $user_id = intval($user_id); //Just in case.

  //$display_name_data = $wpdb->get_var( "SELECT display_name FROM $table_name_users WHERE id= '$user_id_data'" ); //And this is why I didn't call it the entire row by arrow. We are in 4d with multiple tables
  $display_name_data_query = "SELECT display_name FROM ". $table_name_users . " WHERE ID = %d"; //Note: Pulling from WP users table
  $display_name_data_query_prepared = $wpdb->prepare( $display_name_data_query, $user_id );
  $display_name_data = $wpdb->get_var( $display_name_data_query_prepared );

  //Return it out as a string.
  return $display_name_data;
}
