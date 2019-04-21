<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Timer here since more than one shortcode using it now

//Oh. Maybe I should put this elsewhere but I have foudn this nifty code off https://stackoverflow.com/questions/8273804/convert-seconds-into-days-hours-minutes-and-seconds
//So I'm putting it here as a function. Will use elsewhere mayhaps. If so will fix later.
//NOTE: This is the time converstion
function vyps_secondsToTime($seconds)
{
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes, and %s seconds');
}

//Additional functions

//NOTE: Time check.
//I'm putting this here as you might want to know you still have time before you can click the button before you click the button
//Should only check if we have a time check in 'teh' short codes
//No miliseconds. I'm not sorry.
function vyps_point_check_last_transaction_time($user_id, $point_id, $vyps_meta_id)
{
  //$WPDB calls
	global $wpdb;
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_points = $wpdb->prefix . 'vyps_points'; //I'm debating if we needed this but I realize I should check at some point if point actually array_key_exists

  $last_transfer_query = "SELECT max(id) FROM ". $table_name_log . " WHERE user_id = %d AND point_id = %d AND vyps_meta_id = %s"; //In theory we should check for the pid as well, but it the btn should make it unique
  $last_transfer_query_prepared = $wpdb->prepare( $last_transfer_query, $user_id, $point_id, $vyps_meta_id );
  $last_transfer_id = $wpdb->get_var( $last_transfer_query_prepared ); //Now we know the last id. NOTE: It is possible that there was not a previous transaction.

  //return $last_transfer; //DEBUG I think there is something going on here that I'm not aware of.

  if ($last_transfer_id != '') //If its not blank
  {
    //We now know the id exists so an entry exists so we need ot check its timed
    $last_posted_time_query = "SELECT time FROM ". $table_name_log . " WHERE id = %d";
    $last_posted_time_query_prepared = $wpdb->prepare($last_posted_time_query, $last_transfer_id ); //The ids should all be unique. In theory and in practice.
    $last_posted_time = $wpdb->get_var( $last_posted_time_query_prepared ); //Now we know time of the last transaction

    //return $last_posted_time; //DEBUG seeing what the time is.

    $last_posted_time = strtotime($last_posted_time); //Note sure why the 'new' but it was how PHP man suggested to do it

    return $last_posted_time; //This is just raw time being reported. The rest has to be handled by shortcode.
  }
  else
  {
    return 0; //Well if there was no time, it was the start of the epoch clock which is improbable but we know there never has been a transaction
  }
  //else we get nothing. Maybe I should retrun 0
}
