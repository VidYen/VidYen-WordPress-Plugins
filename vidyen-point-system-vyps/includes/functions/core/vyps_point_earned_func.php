<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//vyps_pointearned function
//NOTE: I realized I needed a function with all the adds without subtracted to see total amount earned at some point.
//:ike a historical earnings

/*** POINT Earned FUNCTION ***/
function vyps_point_earned_func($point_id, $user_id)
{
  //The usual suspects to get the sql calls up
  global $wpdb;
	$table_name_points = $wpdb->prefix . 'vyps_points';

  //balance
	//$balance_points = $wpdb->get_var( "SELECT sum(points_amount) FROM $table_vyps_log WHERE user_id = $userID AND points = $point_id"); //Oooh. I love it when I get my variable names the same.
	$balance_points_query = "SELECT sum(points_amount) FROM ". $table_name_log . " WHERE user_id = %d AND point_id = %d AND points_amount > 0"; //NOTE: Only summing positive numbers
	$balance_points_query_prepared = $wpdb->prepare( $balance_points_query, $user_id, $point_id ); //NOTE: Originally this said $current_user_id but although I could pass it through to something else it would not be true if admin specified a UID. Ergo it should just say it $userID
	$balance_points = $wpdb->get_var( $balance_points_query_prepared );

  $balance_points = intval($balance_points); //This should go out as an raw in. The name and icon can be printed seperatly as needed.

  //Return it out as an int
  return $balance_points;
}
