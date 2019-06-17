<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** AJAX TO GRAB HASH PER SECOND FROM MO ***/

//register the ajax for non authenticated users
//NOTE: Non-authed users (those in LoA)
add_action( 'wp_ajax_nopriv_vidyen_rts_train_soldiers_action', 'vidyen_rts_train_soldiers_action' );

// register the ajax action for authenticated users
add_action('wp_ajax_vidyen_rts_train_soldiers_action', 'vidyen_rts_train_soldiers_action');

//register the ajax for non authenticated users
//NOTE: for missions they need to be authenticated
//add_action( 'wp_ajax_nopriv_vidyen_rts_train_soldiers_action', 'vidyen_rts_train_soldiers_action' );

// handle the ajax request
function vidyen_rts_train_soldiers_action()
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
    $user_logged_in = TRUE;
  }
  else
  {
    wp_die();
  }

  global $wpdb; // this is how you get access to the database

  //Assumption. We know the user send 20 soldiers... so we figure out how many survived.
  $solider_point_id = vyps_rts_sql_light_soldier_id_func();
  $solider_icon = vyps_point_icon_func($solider_point_id);
  $laborer_point_id = vyps_rts_sql_laborer_id_func();
  $laborer_icon = vyps_point_icon_func($laborer_point_id);
  $money_sent = 1000;
  $laborers_required = 100;
  $recruit_reason = 'Money Spent';
  $vyps_meta_id = 'train_soldiers';

  //Resource IDs
  $currency_point_id = vyps_rts_sql_currency_id_func();
  $currency_icon = vyps_point_icon_func($currency_point_id);

  //We need to see if they have 1000 copper and 100 laborers soldiers to send

  //And see if they are logged in. I'm using variables as I believe checking to log in uses more checking power
  if ($user_logged_in == FALSE)
  {
    $current_currency_amount = vidyen_mmo_wm_point_balance_func($currency_point_id, $game_id);
    $laborer_amount = vidyen_mmo_wm_point_balance_func($laborer_point_id, $game_id);
  }
  else
  {
    $current_currency_amount = vyps_point_balance_func($currency_point_id, $user_id);
    $laborer_amount = vyps_point_balance_func($laborer_point_id, $user_id);
  }

  if ($current_currency_amount < $money_sent )
  {
    $story = "You don't have enough currency to begin training.";
    $loot = "No soldiers trained.";

    $money_spent = 0;

    $soldiers_trained = 0;

    $village_rts_train_soldiers_server_response = array(
        'system_message' => 'NOTENOUGHMONEY',
        'mission_story' => $story,
        'mission_loot' => $loot,
        'soldiers_trained' => $soldiers_trained,
        'money_spent' => $money_spent,
        'time_left' => 0,
    );
      echo json_encode($village_rts_train_soldiers_server_response); //Proper method to return json
      wp_die(); // this is required to terminate immediately and return a proper response
  }

  if ($laborer_amount < $laborers_required )
  {
    $story = "You don't have enough laborers begin training.";
    $loot = "No soldiers trained.";

    $money_spent = 0;

    $soldiers_trained = 0;

    $village_rts_train_soldiers_server_response = array(
        'system_message' => 'NOTENOUGHLABORERS',
        'mission_story' => $story,
        'mission_loot' => $loot,
        'soldiers_trained' => $soldiers_trained,
        'money_spent' => $money_spent,
        'time_left' => 0,
    );
      echo json_encode($village_rts_train_soldiers_server_response); //Proper method to return json
      wp_die(); // this is required to terminate immediately and return a proper response
  }

  $mission_id = 'trainSoldiers05'; //five minute village sack
  $mission_time = 300; //5 minutes
  $reason = 'Train Soldiers';
  $vyps_meta_id = ''; //I can't think what to use here.

  //First lets check if a mission is currently running.
  $current_mission_time = vidyen_rts_check_mission_time_func($user_id, $mission_id, $mission_time, $game_id);

  //$current_mission_time = 0; //Testing if its this function or the other one

  //In case this is the first mission.
  if ($current_mission_time < 1 )
  {
      //Ok lets set out and conquer!
      $mission_add_result = vidyen_rts_add_mission_func( $mission_id, $mission_time, $user_id, $reason, $vyps_meta_id, $game_id );
      //In my mental stupdity, I forgot to update after adding.
      $current_mission_time = vidyen_rts_check_mission_time_func($user_id, $mission_id, $mission_time, $game_id);
  }
  else
  {
    $story = "You must wait until the laborers recover before attempting to train them again.";
    $loot = "You need to wait $current_mission_time seconds before training again.";

    $money_spent = 0;

    $soldiers_trained = 0;

    $village_rts_train_soldiers_server_response = array(
        'system_message' => 'YOUMUSTWAIT',
        'mission_story' => $story,
        'mission_loot' => $loot,
        'soldiers_trained' => $soldiers_trained,
        'money_spent' => $money_spent,
        'time_left' => $current_mission_time,
    );
      echo json_encode($village_rts_train_soldiers_server_response); //Proper method to return json
      wp_die(); // this is required to terminate immediately and return a proper response
  }

  $money_spent = mt_rand( 0 , $money_sent ); //It's possible the laborders were really bored
  $soldiers_trained = mt_rand( 0 , 100 );

  if ($money_spent < 250)
  {
    $story = "You order the training of laborers into soldiers. They are tired of working in the field so you get $solider_icon $soldiers_trained for only $currency_icon $money_spent.";
    $loot = "You received $soldiers_trained soldiers.";

    //Technically no soldiders were killed.
  }
  elseif ($money_spent < 500)
  {
    $soldiers_trained = $soldiers_trained + 10; //I figure you get a bonus
    $story = "The laborers seem restless but lazy so you throw a bit more money at them.. The extra money you spent earned a bonus of $solider_icon 10 for a total of $solider_icon $soldiers_trained costing $currency_icon $money_spent.";
    $loot = "You received $soldiers_trained laborers." ;
  }
  elseif ($money_spent < 750)
  {
    $soldiers_trained = $soldiers_trained + 15; //I figure you get a bonus
    $story = "Not only are these laborers lazy, you need to hire a man to whip them into shape but you get a bonus of $solider_icon 15 for a total of $solider_icon $soldiers_trained costing $currency_icon $money_spent.";
    $loot = "You received $soldiers_trained laborers." ;
  }
  elseif ($money_spent < 2000)
  {
    $soldiers_trained = $soldiers_trained + 20; //I figure you get a bonus
    $story = "You spend most of your money with $currency_icon  $money_spent spent. The amount of money did get extra 20 $solider_icon with a total of $solider_icon $soldiers_trained";
    $loot = "You received $soldiers_trained laborers." ;
  }
  elseif ($money_spent > 1999)
  {
    $story = "You hand over all the money intended for training of $currency_icon $money_spent but all of these particular laborers are incompetant and you ralize you wasted all your money.";
    $loot = "You received 0 soldiers.";
    $soldiers_trained = 0;
  }
  else
  {
    $story = "Error?";
    $loot = "RNGJesus save us!";
  }

  //Time to remove currency via functions.
  vyps_point_deduct_func( $currency_point_id, $money_spent, $user_id, $recruit_reason, $vyps_meta_id, $game_id);

  //Time to remove laborers via functions.
  vyps_point_deduct_func( $laborer_point_id, $soldiers_trained, $user_id, $recruit_reason, $vyps_meta_id );

  //Time to credit resources. I'm being lazy and getting the whole response sum so i can see in js (this whole thing was made in 2 hours)
  $response_sum = vyps_point_credit_func( $solider_point_id, $soldiers_trained, $user_id, $recruit_reason, $vyps_meta_id, $vyps_meta_data = '', $vyps_meta_subid1 = '', $vyps_meta_subid2 ='', $vyps_meta_subid3= '', $game_id);

  $village_rts_train_soldiers_server_response = array(
      'system_message' => $response_sum,
      'mission_story' => $story,
      'mission_loot' => $loot,
      'soldiers_trained' => $soldiers_trained,
      'money_spent' => $money_spent,
      'time_left' => $current_mission_time,
  );

  echo json_encode($village_rts_train_soldiers_server_response); //Proper method to return json

  wp_die(); // this is required to terminate immediately and return a proper response
}
