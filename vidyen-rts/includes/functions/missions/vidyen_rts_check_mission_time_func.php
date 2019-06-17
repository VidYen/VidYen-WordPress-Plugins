<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Timer here since more than one shortcode using it now

//Oh. Maybe I should put this elsewhere but I have foudn this nifty code off https://stackoverflow.com/questions/8273804/convert-seconds-into-days-hours-minutes-and-seconds
//So I'm putting it here as a function. Will use elsewhere mayhaps. If so will fix later.
//NOTE: This is the time converstion
function vidyen_rts_seconds_to_days($seconds)
{
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes, and %s seconds');
}

//Additional functions

//NOTE: Time check for last mission. Since the mission log is only known missions,
//And missions will be unique so don't need meta id
//Mission time should be called from somewhere. For now its being pulled form the funciton call
//I may include this into the mission table

function vidyen_rts_check_mission_time_func($user_id, $mission_id, $mission_time, $game_id = '')
{
  //$WPDB calls
	global $wpdb;
	$table_name_log = $wpdb->prefix . 'vidyen_rts_mission_log';

  if ($user_id == 0 OR $user_id == '')
  {
    //NOTE: I realized I'd have to have two but slightly similar situations if user_id was 0
    //As the game_id is not an integer
    $last_transfer_query = "SELECT max(id) FROM ". $table_name_log . " WHERE game_id = %s AND mission_id = %s"; //In theory we should check for the pid as well, but it the btn should make it unique
    $last_transfer_query_prepared = $wpdb->prepare( $last_transfer_query, $game_id, $mission_id);
    $last_transfer_id = $wpdb->get_var( $last_transfer_query_prepared ); //Now we know the last id. NOTE: It is possible that there was not a previous transaction.

    //return $last_transfer; //DEBUG I think there is something going on here that I'm not aware of.
  }
  else
  {
    //THIS IS WHEN $user_id is not 0
    $last_transfer_query = "SELECT max(id) FROM ". $table_name_log . " WHERE user_id = %d AND mission_id = %s"; //In theory we should check for the pid as well, but it the btn should make it unique
    $last_transfer_query_prepared = $wpdb->prepare( $last_transfer_query, $user_id, $mission_id);
    $last_transfer_id = $wpdb->get_var( $last_transfer_query_prepared ); //Now we know the last id. NOTE: It is possible that there was not a previous transaction.

    //return $last_transfer; //DEBUG I think there is something going on here that I'm not aware of.
  }

  if ($last_transfer_id != '') //If its not blank
  {
    //We now know the id exists so an entry exists so we need ot check its timed
    $last_posted_time_query = "SELECT time FROM ". $table_name_log . " WHERE id = %d";
    $last_posted_time_query_prepared = $wpdb->prepare($last_posted_time_query, $last_transfer_id ); //The ids should all be unique. In theory and in practice.
    $last_posted_time = $wpdb->get_var( $last_posted_time_query_prepared ); //Now we know time of the last transaction

    //return $last_posted_time; //DEBUG seeing what the time is.

    $last_posted_time = strtotime($last_posted_time); //Note sure why the 'new' but it was how PHP man suggested to do it

    //NOTE: I believe this function should show how much time we have left. Easier to functionalize it all.
    $current_time = date('Y-m-d H:i:s');
    $current_time = strtotime($current_time);
    $time_passed = $current_time - $last_posted_time; //I'm just making a big guess here that this will work.
    $time_left = intval($mission_time) - $time_passed; //This may result in a negative number

    return $time_left; //This is just raw time being reported. The rest has to be handled by shortcode.
  }
  else
  {
    return 0; //Well if there was no time, it was the start of the epoch clock which is improbable but we know there never has been a transaction
  }
  //else we get nothing. Maybe I should retrun 0
}
