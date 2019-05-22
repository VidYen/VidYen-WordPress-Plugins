<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** AJAX TO GRAB HASH PER SECOND FROM MO ***/

// register the ajax action for authenticated users
add_action('wp_ajax_vidyen_rts_recruit_laborers_timer_action', 'vidyen_rts_recruit_laborers_timer_action');

//register the ajax for non authenticated users
//NOTE: for missions they need to be authenticated
//add_action( 'wp_ajax_nopriv_vidyen_rts_sack_village_action', 'vidyen_rts_sack_village_action' );

// handle the ajax request
function vidyen_rts_recruit_laborers_timer_action()
{
  global $wpdb; // this is how you get access to the database

  //Is user logged in check!
  if ( ! is_user_logged_in() )
  {
    $village_rts_sack_village_server_response = array(
        'system_message' => "NOTLOGGEDIN",
    );
      echo json_encode($village_rts_sack_village_server_response); //Proper method to return json
      wp_die(); // this is required to terminate immediately and return a proper response
  }

  $user_id = get_current_user_id();

  $mission_id = 'sackvillage05'; //five minute village sack
  $mission_time = 300; //5 minutes
  $reason = 'Sack the village!';
  $vyps_meta_id = ''; //I can't think what to use here.

  //First lets check if a mission is currently running.
  $current_mission_time = vidyen_rts_check_mission_time_func($user_id, $mission_id, $mission_time);

  $village_rts_sack_village_server_response = array(

      'time_left' => $current_mission_time,
  );

  echo json_encode($village_rts_sack_village_server_response); //Proper method to return json

  wp_die(); // this is required to terminate immediately and return a proper response
}
