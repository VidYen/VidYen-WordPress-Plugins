<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** AJAX TO GRAB HASH PER SECOND FROM MO ***/

// register the ajax action for authenticated users
add_action('wp_ajax_vyps_rts_bal_api_action', 'vyps_rts_bal_api_action');

//register the ajax for non authenticated users
//add_action( 'wp_ajax_nopriv_vyps_momo_api_action', 'vyps_rts_bal_api_action' ); //Should not be there for now

// handle the ajax request
function vyps_rts_bal_api_action()
{
  global $wpdb; // this is how you get access to the database

  //NOTE: I do not think there is a need for nonce as no user input to wordpress

  //Military
  $light_solider_point_id = vyps_rts_sql_light_soldier_id_func();

  //Resource IDs
  $currency_point_id = vyps_rts_sql_currency_id_func();
  $wood_point_id = vyps_rts_sql_wood_id_func();
  $iron_point_id = vyps_rts_sql_iron_id_func();
  $stone_point_id = vyps_rts_sql_stone_id_func();

  //Get user id
  $user_id = get_current_user_id();

  $currency_balance = intval(vyps_point_balance_func($currency_point_id, $user_id)); //Yes we always be santising so whelp
  $light_soldier_balance = intval(vyps_point_balance_func($light_solider_point_id, $user_id)); //Yes we always be santising so whelp
  $wood_balance = intval(vyps_point_balance_func($wood_point_id, $user_id)); //Yes we always be santising so whelp
  $iron_balance = intval(vyps_point_balance_func($iron_point_id, $user_id)); //Yes we always be santising so whelp
  $stone_balance = intval(vyps_point_balance_func($stone_point_id, $user_id)); //Yes we always be santising so whelp
.
  $rts_bal_array_server_response = array(
      'currency_balance' => $currency_balance,
      'light_soldier_balance' => $light_soldier_balance,
      'wood_balance' => $wood_balance,
      'iron_balance' => $iron_balance,
      'stone_balance' => $stone_balance,
  );

  echo json_encode($rts_bal_array_server_response); //Proper method to return json

  wp_die(); // this is required to terminate immediately and return a proper response
}
