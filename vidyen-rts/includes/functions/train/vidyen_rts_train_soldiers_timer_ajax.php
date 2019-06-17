<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** AJAX TO GRAB HASH PER SECOND FROM MO ***/

//register the ajax for non authenticated users
//NOTE: Non-authed users (those in LoA)
add_action( 'wp_ajax_nopriv_vidyen_rts_train_soldiers_timer_action', 'vidyen_rts_train_soldiers_timer_action' );

// register the ajax action for authenticated users
add_action('wp_ajax_vidyen_rts_train_soldiers_timer_action', 'vidyen_rts_train_soldiers_timer_action');

//register the ajax for non authenticated users
//NOTE: for missions they need to be authenticated
//add_action( 'wp_ajax_nopriv_vidyen_rts_sack_village_action', 'vidyen_rts_sack_village_action' );

// handle the ajax request
function vidyen_rts_train_soldiers_timer_action()
{
  if (!is_user_logged_in())
  {
    if (!isset($_POST['user_id']))
    {
      wp_die(); //If the game_id didn't come through then it means the get from the above didnt' work
                //and by all accounts it should die at that point.
    }
    else
    {
      $game_id = sanitize_text_field( $_POST['user_id'] ); //If its good enough for the Romans, it's good enough for me.
      $user_id = 0; //Signal that user has a user_id but not logged in
      $user_logged_in = FALSE;
    }
  }
  elseif (is_user_logged_in())
  {
    //Either user is logged in or they isn't.
    $user_id = get_current_user_id();
    $game_id = '';
  }
  else
  {
    wp_die();
  }

  global $wpdb; // this is how you get access to the database

  $mission_id = 'trainsoldiers05'; //five minute village sack
  $mission_time = 300; //5 minutes
  $reason = 'Train Soldiers';
  $vyps_meta_id = ''; //I can't think what to use here.

  //First lets check if a mission is currently running.
  $current_mission_time = vidyen_rts_check_mission_time_func($user_id, $mission_id, $mission_time);

  $village_rts_train_soldiers_server_response = array(

      'time_left' => $current_mission_time,
  );

  echo json_encode($village_rts_train_soldiers_server_response); //Proper method to return json

  wp_die(); // this is required to terminate immediately and return a proper response
}
