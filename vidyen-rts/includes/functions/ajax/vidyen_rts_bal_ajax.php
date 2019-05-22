<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// register the ajax action for authenticated users
add_action('wp_ajax_vidyen_rts_bal_api_action', 'vidyen_rts_bal_api_action');

//register the ajax for non authenticated users
//add_action( 'wp_ajax_nopriv_vyps_momo_api_action', 'vidyen_rts_bal_api_action' ); //Should not be there for now

// handle the ajax request
function vidyen_rts_bal_api_action()
{
  global $wpdb; // this is how you get access to the database

    if ( ! is_user_logged_in() )
    {
      wp_die(); // this is required to terminate immediately and return a proper response
    }

  //NOTE: I do not think there is a need for nonce as no user input to wordpress

  //Military
  $light_solider_point_id = vyps_rts_sql_light_soldier_id_func();

  //Civilian
  $laborer_point_id = vyps_rts_sql_laborer_id_func();

  //Resource IDs
  $currency_point_id = vyps_rts_sql_currency_id_func();
  $wood_point_id = vyps_rts_sql_wood_id_func();
  $iron_point_id = vyps_rts_sql_iron_id_func();
  $stone_point_id = vyps_rts_sql_stone_id_func();

  //Get user id
  $user_id = get_current_user_id();

  //Soldier Balance
  $light_soldier_balance = intval(vyps_point_balance_func($light_solider_point_id, $user_id));

  //Laborer Balance
	$laborer_balance = intval(vyps_point_balance_func($laborer_point_id, $user_id));

  //Resource Balance
  $currency_balance = intval(vyps_point_balance_func($currency_point_id, $user_id));
  $wood_balance = intval(vyps_point_balance_func($wood_point_id, $user_id));
  $iron_balance = intval(vyps_point_balance_func($iron_point_id, $user_id));
  $stone_balance = intval(vyps_point_balance_func($stone_point_id, $user_id));

  $rts_bal_array_server_response = array(
      'currency_balance' => $currency_balance,
      'light_soldier_balance' => $light_soldier_balance,
      'laborer_balance' => $laborer_balance,
      'wood_balance' => $wood_balance,
      'iron_balance' => $iron_balance,
      'stone_balance' => $stone_balance,
  );

  echo json_encode($rts_bal_array_server_response); //Proper method to return json
  wp_die(); // this is required to terminate immediately and return a proper response
}
