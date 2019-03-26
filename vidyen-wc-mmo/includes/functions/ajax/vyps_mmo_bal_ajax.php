<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** AJAX TO GRAB HASH PER SECOND FROM MO ***/

// register the ajax action for authenticated users
add_action('wp_ajax_vyps_mmo_bal_api_action', 'vyps_mmo_bal_api_action');

//register the ajax for non authenticated users
//add_action( 'wp_ajax_nopriv_vyps_momo_api_action', 'vyps_mmo_bal_api_action' ); //Should not be there for now

// handle the ajax request
function vyps_mmo_bal_api_action()
{
  global $wpdb; // this is how you get access to the database

  //NOTE: I do not think there is a need for nonce as no user input to wordpress

  //Post gather from the AJAX post
  //$point_id = intval($_POST['point_id']);
  $point_id = vyps_mmo_sql_point_id_func();
  $user_id = get_current_user_id();

  $point_balance = intval(vyps_point_balance_func($point_id, $user_id)); //Yes we always be santising so whelp
  $ww_balance = sanitize_text_field(vyps_woowallet_bal_func($user_id)); //Sadly WooWallet returns a text field. I need to figure this out down the road.


  $mmo_bal_array_server_response = array(
      'point_balance' => $point_balance,
      'ww_balance' => $ww_balance,
  );

  echo json_encode($mmo_bal_array_server_response); //Proper method to return json

  wp_die(); // this is required to terminate immediately and return a proper response
}
