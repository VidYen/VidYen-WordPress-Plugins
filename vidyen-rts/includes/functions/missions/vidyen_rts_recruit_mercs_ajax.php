<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** AJAX TO GRAB HASH PER SECOND FROM MO ***/

// register the ajax action for authenticated users
add_action('wp_ajax_vidyen_rts_sack_village_action', 'vidyen_rts_sack_castle_action');

//register the ajax for non authenticated users
//NOTE: for missions they need to be authenticated
//add_action( 'wp_ajax_nopriv_vidyen_rts_sack_village_action', 'vidyen_rts_sack_village_action' );

// handle the ajax request
function vidyen_rts_sack_castle_action()
{
  global $wpdb; // this is how you get access to the database

  //Is user logged in check!
  if ( ! is_user_logged_in() )
  {
    $castle_rts_sack_castle_server_response = array(
        'system_message' => "NOTLOGGEDIN",
    );
      echo json_encode($castle_rts_sack_castle_server_response); //Proper method to return json
      wp_die(); // this is required to terminate immediately and return a proper response
  }

  $user_id = get_current_user_id();

  //Assumption. We know the user send 20 soldiers... so we figure out how many survived.
  $solider_point_id = vyps_rts_sql_light_soldier_id_func();
  $soldiers_sent = 500;
  $soldiers_reason = 'Soldiers killed';
  $vyps_meta_id = 'Mission_sack';

  //Resource IDs
  $currency_point_id = vyps_rts_sql_currency_id_func();
  $wood_point_id = vyps_rts_sql_wood_id_func();
  $iron_point_id = vyps_rts_sql_iron_id_func();
  $stone_point_id = vyps_rts_sql_stone_id_func();

  //We need to see if they have 20 soldiers to send

  $current_soldier_amount = vyps_point_balance_func($solider_point_id, $user_id);
  if ($current_soldier_amount < $soldiers_sent)
  {
    $story = "You don't have enough soldiers to send. They refuse to budge from the barracks.";
    $loot = "You received 0 resources.";

    $soldiers_killed = 0;

    $money_looted = 0;
    $wood_looted = 0;
    $iron_looted = 0;
    $stone_looted = 0;

    $castle_rts_sack_castle_server_response = array(
        'system_message' => 'NOTENOUGHSOLDIERS',
        'mission_story' => $story,
        'mission_loot' => $loot,
        'money_looted' => $money_looted2,
        'wood_looted' => $wood_looted,
        'iron_looted' => $iron_looted,
        'stone_looted' => $stone_looted,
        'soldiers_killed' => $soldiers_killed,
        'time_left' => 0,
    );
      echo json_encode($castle_rts_sack_castle_server_response); //Proper method to return json
      wp_die(); // this is required to terminate immediately and return a proper response
  }

  $mission_id = 'sackcastle05'; //five minute village sack
  $mission_time = 30; //5 minutes
  $reason = 'Sack the castle!';
  $vyps_meta_id = ''; //I can't think what to use here.

  //First lets check if a mission is currently running.
  $current_mission_time = vidyen_rts_check_mission_time_func($user_id, $mission_id, $mission_time);

  //$current_mission_time = 0; //Testing if its this function or the other one

  //In case this is the first mission.
  if ($current_mission_time < 1 )
  {
      //Ok lets set out and conquer!
      $mission_add_result = vidyen_rts_add_mission_func( $mission_id, $mission_time, $user_id, $reason, $vyps_meta_id );
      //In my mental stupdity, I forgot to update after adding.
      $current_mission_time = vidyen_rts_check_mission_time_func($user_id, $mission_id, $mission_time);
  }
  else
  {
    $story = "You must wait until local castle's to recover before taking advantage of them.";
    $loot = "You need to wait $current_mission_time seconds before pillaging again.";

    $soldiers_killed = 0;

    $money_looted = 0;
    $wood_looted = 0;
    $iron_looted = 0;
    $stone_looted = 0;

    $castle_rts_sack_castle_server_response = array(
        'system_message' => 'YOUMUSTWAIT',
        'mission_story' => $story,
        'mission_loot' => $loot,
        'money_looted' => $money_looted2,
        'wood_looted' => $wood_looted,
        'iron_looted' => $iron_looted,
        'stone_looted' => $stone_looted,
        'soldiers_killed' => $soldiers_killed,
        'time_left' => $current_mission_time,
    );
      echo json_encode($castle_rts_sack_castle_server_response); //Proper method to return json
      wp_die(); // this is required to terminate immediately and return a proper response
  }


  $soldiers_killed = mt_rand( 0 , $soldiers_sent ); //It's possible no one died.

  $money_looted = mt_rand( 0 , 5000 );
  $wood_looted = mt_rand( 0 , 1000 );
  $iron_looted = mt_rand( 0 , 600 );
  $stone_looted = mt_rand( 0 , 600 ); //Why would you loot stone?

//  $soldiers_killed1 = 5000 - $soldiers_killed(25);
//  if ($money_looted1 > $soldiers_killed1)
//  {

 //$money_looted1 = $money_looted;
 //$soldiers_killed1 = $soldiers_killed(25);
//  }  $soldiers_killed1 = 5000 - $soldiers_killed(25);
  //if ($money_looted1 > $soldiers_killed1)
  //{
    //global $money_looted2 = $soldiers_killed1;
    //$money_looted2 = 200;
    if ($money_looted2 > $money_looted ){
}
    elseif ($soldiers_killed < 1 || $money_looted2 > $soldiers_killed1)
    {
      //$lootss = 5000;
      //$money_looted2 = $lootss - $soldiers_killed(25);
      $story = "Your scouts go out to recruit soliders from the local mercenary hall. They arrive and quickly start chatting with the mercenaries coaxing over a lot of good soliders for your barracks";
      $loot = "You received $solider_point_id soliders.";

      //Technically no soldiders were killed.
    }
    elseif ($soldiers_killed < 100)
    {
      //intval($soldiers_killed)
      $story = "Your scouts leave to recruit mercenaries and come back with a few good ones but it doesnt seem like too many people were interested.";
      $loot = "You received $solider_point_id soliders.";
      $stone_looted = 0; //Just had to reset that.
    }
    elseif ($soldiers_killed < 250)
    {
      //$money_looted2 = $lootss - $soldiers_killed(25);
      $story = "Your soldiers attack using bows however one of them in the back gets trigger happy, upon shotting the guards of the mine he doesnt stop there and kills many people before being taken down with $soldiers_killed soldiers. In the end they walk away with far fewer men along with one in ropes tied up. They remaining soldiers have to haul back the stone and iron without help from the previously large force.";
      $loot = "You received $solider_point_id soliders.";
      $stone_looted = 0; //Just had to reset that.
      $iron_looted = 0;
    }
    elseif ($soldiers_killed < 500)
    {
      //$money_looted2 = $lootss - $soldiers_killed(25);
      $story = "Your soldiers raid the mine but the majority of them die under heavy siege from a group of castle mages who were passing by very bad luck indeed with $soldiers_killed soldiers lost. They only carry a paltry amount of iron forgoing stone for the more urgent needs.";
      $loot = "You received $solider_point_id soliders.";
      $stone_looted = 0; //Just had to reset that.
      $iron_looted = 0;
      $wood_looted = 0;
    }
    elseif ($soldiers_killed > 499)
    {
      //$money_looted2 = $lootss - $soldiers_killed(25);
      $story = "Your soldiers take the main road over a stone bridge towards the mine however their march causes the bridge to collpase just as they all are on it leaving all of them to fall to their doom in the endless pit. All with $soldiers_killed soldiers died.";
      $loot = "You received $solider_point_id soliders.";
      $stone_looted = 0; //Just had to reset that.
      $iron_looted = 0;
      $wood_looted = 0;
      $money_looted2 = 0;
    }
    else
    {
      $story = "Error?";
      $loot = "RNGJesus save us!";
    }

  //}
//  ----------------------------------
/*else{
  if ($soldiers_killed < 40)
  {
    $story = "Your soldiers suprise the castle residents while they are throwing a ball and take few casualties with $soldiers_killed killed. They even tear down parts of the castle carrying back raw materials.";
    $loot = "You received $money_looted copper coins, $wood_looted wood, $iron_looted iron ore, and $stone_looted units of stone.";

    //Technically no soldiders were killed.
  }//fdfg
  elseif ($soldiers_killed < 80)
  {
    $story = "Your soldiers attack and take some light casualties with $soldiers_killed soldiers lost. They remaining are too lazy to haul back stone or weapons from the armoury. Instead they return with many kegs of mead and have their own party.";
    $loot = "You received $money_looted copper coins, $wood_looted wood, and $iron_looted iron ore." ;
    $stone_looted = 0; //Just had to reset that.
  }
  elseif ($soldiers_killed < 130)
  {
    $story = "Your soldiers attack but the castle was only changing posts for the watch towers, upon seeing your men they attack with a volley of archer fire with $soldiers_killed soldiers. In the end they lost and eventually fled the castle. They remaining soldiers are too few to haul back the stone and iron.";
    $loot = "You received $money_looted copper coins and $wood_looted wood.";
    $stone_looted = 0; //Just had to reset that.
    $iron_looted = 0;
  }
  elseif ($soldiers_killed < 200)
  {
    $story = "Your soldiers raid the castle but the majority of them die with $soldiers_killed soldiers lost. They only carry back the copper coins they theived from the treasury.";
    $loot = "You received $money_looted in copper coins." ;
    $stone_looted = 0; //Just had to reset that.
    $iron_looted = 0;
    $wood_looted = 0;
  }
  elseif ($soldiers_killed > 199)
  {
    $story = "Your soldiers were posioned before attacking the castle and started to drop one by one as the approached the castle the guards not having to lift a finger as even the final solider only gets to the gate before collapsing. All with $soldiers_killed soldiers died.";
    $loot = "You received 0 resources.";
    $stone_looted = 0; //Just had to reset that.
    $iron_looted = 0;
    $wood_looted = 0;
    $money_looted = 0;
  }
  else
  {
    $story = "Error?";
    $loot = "RNGJesus save us!";
  }
}
*/
  //Time to remove soldiers via functions.
  vyps_point_deduct_func( $solider_point_id, $soldiers_killed, $user_id, $soldiers_reason, $vyps_meta_id );

  //Time to credit resources. I'm being lazy and getting the whole response sum so i can see in js (this whole thing was made in 2 hours)
  $response_sum = vyps_point_credit_func( $currency_point_id, $money_looted, $user_id, $soldiers_reason, $vyps_meta_id );
  $response_sum = $response_sum + vyps_point_credit_func( $wood_point_id, $wood_looted, $user_id, $soldiers_reason, $vyps_meta_id );
  $response_sum = $response_sum + vyps_point_credit_func( $iron_point_id, $iron_looted, $user_id, $soldiers_reason, $vyps_meta_id );
  $response_sum = $response_sum + vyps_point_credit_func( $stone_point_id, $stone_looted, $user_id, $soldiers_reason, $vyps_meta_id );

  $castle_rts_sack_castle_server_response = array(
      'system_message' => $response_sum,
      'mission_story' => $story,
      'mission_loot' => $loot,
      'money_looted' => $money_looted2,
      'wood_looted' => $wood_looted,
      'iron_looted' => $iron_looted,
      'stone_looted' => $stone_looted,
      'soldiers_killed' => $soldiers_killed,
      'time_left' => $current_mission_time,
  );

  echo json_encode($castle_rts_sack_castle_server_response); //Proper method to return json

  wp_die(); // this is required to terminate immediately and return a proper response
}
