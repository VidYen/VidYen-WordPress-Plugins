<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** AJAX TO GRAB HASH PER SECOND FROM MO ***/

// register the ajax action for authenticated users
add_action('wp_ajax_vidyen_rts_sack_village_action', 'vidyen_rts_sack_village_action');

//register the ajax for non authenticated users
//NOTE: for missions they need to be authenticated
//add_action( 'wp_ajax_nopriv_vidyen_rts_sack_village_action', 'vidyen_rts_sack_village_action' );

// handle the ajax request
function vidyen_rts_sack_village()
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

  //Assumption. We know the user send 20 soldiers... so we figure out how many survived.
  $solider_point_id = vyps_rts_sql_currency_id_func();
  $soldiers_sent = 20;
  $soldiers_reason = 'Soldiers killed';
  $vyps_meta_id = 'Mission_sack';

  //We need to see if they have 20 soldiers to send

  $curreny_soldier_amoutn = vyps_point_balance_func($solider_point_id, $user_id);
  if ($curreny_soldier_amoutn < $soldiers_sent)
  {
    $story = "You don't have enough soldiers to send. They refuse to budge from the barracks."
    $loot = "You received 0 resources."

    $village_rts_sack_village_server_response = array(
        'system_message' => 'NOTENOUGH',
        'mission_story' => $story,
        'loot' => $loot,
    );
      echo json_encode($village_rts_sack_village_server_response); //Proper method to return json
      wp_die(); // this is required to terminate immediately and return a proper response
  }

  $soldiers_killed = mt_rand( 0 , $soldiers_sent ); //It's possible no one died.

  $money_looted = mt_rand( 0 , 1000 );
  $wood_looted = mt_rand( 0 , 200 );
  $iron_looted = mt_rand( 0 , 150 );
  $stone_looted = mt_rand( 0 , 100 ); //Why would you loot stone?

  if ($soldiers_killed < 1)
  {
    $story = "You're soldiers suprise the villagers pillaging them all and taking no casualties. They even tore down the poor stone huts carrying back stone.";
    $loot = "You received $money_looted copper coins, $wood_looted wood, $iron_looted iron ore, and $stone_looted units of stone.";

    //Technically no soldiders were killed.
  }
  elseif ($soldiers_killed < 5)
  {
    $story = "You're soldiers attack and take some light casualties. They remaining are too lazy to haul back the stone. It's heavy.";
    $loot = "You received $money_looted copper coins, $wood_looted wood, and $iron_looted iron ore." ;
    $stone_looted = 0; //Just had to reset that.
  }
  elseif ($soldiers_killed < 10)
  {
    $story = "You're soldiers attack but the villages were angry and killed a good deal of your men but eventually fled. They remaining soldiers are too lazy to haul back stone and iron.";
    $loot = "You received $money_looted copper coins and $wood_looted wood.";
    $stone_looted = 0; //Just had to reset that.
    $iron_looted = 0;
  }
  elseif ($soldiers_killed < 15)
  {
    $story = "You're sack the village but most of them die. They only carry back the copper coins they theived.";
    $loot = "You received $money_looted in copper coins." ;
    $stone_looted = 0; //Just had to reset that.
    $iron_looted = 0;
    $wood_looted = 0;
  }
  elseif ($soldiers_killed == 20)
  {
    $story = "You're soldiers got drunk before attacking the peasants and attacked a nearby castle instead. They all died.";
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

  //Time to remove soldiers via functions.
  vyps_point_deduct_func( $solider_point_id, $soldiers_killed, $user_id, $soldiers_reason, $vyps_meta_id );

  //Time to credit resources.
  vyps_point_credit_func( $solider_point_id, $soldiers_killed, $user_id, $soldiers_reason, $vyps_meta_id );
  vyps_point_credit_func( $solider_point_id, $soldiers_killed, $user_id, $soldiers_reason, $vyps_meta_id );
  vyps_point_credit_func( $solider_point_id, $soldiers_killed, $user_id, $soldiers_reason, $vyps_meta_id );
  vyps_point_credit_func( $solider_point_id, $soldiers_killed, $user_id, $soldiers_reason, $vyps_meta_id );

  $mo_array_server_response = array(
      'site_hashes' => $site_total_hashes,
      'site_hash_per_second' => $site_hash_per_second,
      'site_validShares' => $site_valid_shares,
      'current_XMRprice' => $current_xmr_price,
  );

  echo json_encode($mo_array_server_response); //Proper method to return json

  wp_die(); // this is required to terminate immediately and return a proper response
}
