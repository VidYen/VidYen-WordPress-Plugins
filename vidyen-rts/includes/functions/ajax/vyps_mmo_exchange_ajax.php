<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** AJAX TO GRAB HASH PER SECOND FROM MO ***/

// register the ajax action for authenticated users
add_action('wp_ajax_vyps_mmo_exchange_api_action', 'vyps_mmo_exchange_api_action');

//register the ajax for non authenticated users
//add_action( 'wp_ajax_nopriv_vyps_momo_api_action', 'vyps_mmo_exchange_api_action' ); //Should not be there for now

// handle the ajax request
function vyps_mmo_exchange_api_action()
{
  global $wpdb; // this is how you get access to the database

  //NOTE: I do not think there is a need for nonce as no user input to wordpress

  //Post gather from the AJAX post
  //$point_id = intval($_POST['point_id']);
  $point_id = vyps_mmo_sql_point_id_func();
  $point_amount = intval(vyps_mmo_sql_point_amount_func()); //Get how much we are deducting
  $output_amount = floatval(vyps_mmo_sql_output_amount_func()); //The amount we give to WooWallet
  $user_id = get_current_user_id();
  $reason = 'VidYen Point Exchange';

  $prior_balance = intval(vyps_point_balance_func($point_id, $user_id));

  if($prior_balance >= $point_amount)
  {
    $deduct_result = vyps_point_deduct_func( $point_id, $point_amount, $user_id, $reason, $vyps_meta_id );
    $lacking_balance = 0;
    $point_balance = vyps_point_balance_func($point_id, $user_id);
    $add_result = vyps_ww_point_credit_func( $user_id, $output_amount, $reason );
  }
  else
  {
    $lacking_balance = intval($point_amount - $prior_balance);
    $point_balance = vyps_point_balance_func($point_id, $user_id);
    $deduct_result = 'Not run';
    $add_result = 'Not run';
  }



  $mmo_exchange_array_server_response = array(
      'point_balance' => $point_balance,
      'needed_balance' => $lacking_balance,
      'deduct_result' => $deduct_result,
      'add_result' => $add_result

  );

  echo json_encode($mmo_exchange_array_server_response); //Proper method to return json

  wp_die(); // this is required to terminate immediately and return a proper response
}
