<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** AJAX TO GRAB HASH PER SECOND FROM MO ***/

// register the ajax action for authenticated users
add_action('wp_ajax_vyps_mmo_bal_api_action', 'vyps_mmo_bal_api_action');

//register the ajax for non authenticated users
add_action( 'wp_ajax_nopriv_vyps_momo_api_action', 'vyps_mmo_bal_api_action' );

// handle the ajax request
function vyps_mmo_bal_api_action()
{
  global $wpdb; // this is how you get access to the database

  //NOTE: I do not think there is a need for nonce as no user input to wordpress

  //Post gather from the AJAX post
  $point_id = sanitize_text_field($_POST['point_id']);
  

  $mo_array_server_response = array(
      'site_hashes' => $site_total_hashes,
      'site_hash_per_second' => $site_hash_per_second,
      'site_validShares' => $site_valid_shares,
      'current_XMRprice' => $current_xmr_price,
  );

  echo json_encode($mo_array_server_response); //Proper method to return json

  wp_die(); // this is required to terminate immediately and return a proper response
}
