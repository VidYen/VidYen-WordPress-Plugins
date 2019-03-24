<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//vyps_point_balance function
//I have decided I just need a funciton to get a sum of the current points for current user via the point id
//If the $poinID is 'ww' it means we just pull the balance on that

/*** POINT NAME FUNCTION ***/
function vyps_point_balance_func($point_id)
{
  //The usual suspects to get the sql calls up
  global $wpdb;
	$table_name_points = $wpdb->prefix . 'vyps_points';

  //balance
	//$balance_points = $wpdb->get_var( "SELECT sum(points_amount) FROM $table_vyps_log WHERE user_id = $userID AND points = $point_id"); //Oooh. I love it when I get my variable names the same.
	$balance_points_query = "SELECT sum(points_amount) FROM ". $table_name_log . " WHERE user_id = %d AND point_id = %d";
	$balance_points_query_prepared = $wpdb->prepare( $balance_points_query, $userID, $point_id ); //NOTE: Originally this said $current_user_id but although I could pass it through to something else it would not be true if admin specified a UID. Ergo it should just say it $userID
	$balance_points = $wpdb->get_var( $balance_points_query_prepared );

  $balance_points = intval($balance_points); //This should

  //Return it out as a string.
  return $sourceName;
}
