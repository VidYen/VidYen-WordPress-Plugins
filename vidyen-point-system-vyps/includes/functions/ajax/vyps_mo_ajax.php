<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** AJAX TO GRAB HASH PER SECOND FROM MO ***/

// register the ajax action for authenticated users
add_action('wp_ajax_vyps_mo_api_action', 'vyps_mo_api_action');

//register the ajax for non authenticated users
add_action( 'wp_ajax_nopriv_vyps_mo_api_action', 'vyps_mo_api_action' );

// handle the ajax request
function vyps_mo_api_action()
{
  global $wpdb; // this is how you get access to the database

  //NOTE: I do not think there is a need for nonce as no user input to wordpress

  //Post gather from the AJAX post
  $site_wallet = sanitize_text_field($_POST['site_wallet']);
  $site_worker = sanitize_text_field($_POST['site_worker']);

  //Copy and paste from the Shortcodes
  //MO remote get info for client

  //MO remote get info for site
  $mo_site_wallet = $site_wallet;
  $mo_site_worker = $site_worker;

  /*** MoneroOcean Gets***/
  //Site get
  $site_url = 'https://api.moneroocean.stream/miner/' . $mo_site_wallet . '/stats/' . $mo_site_worker;
  $site_mo_response = wp_remote_get( $site_url );
  if ( is_array( $site_mo_response ) )
  {
    $site_mo_response = $site_mo_response['body']; // use the content
    $site_mo_response = json_decode($site_mo_response, TRUE);
    if (array_key_exists('totalHash', $site_mo_response))
    {
      $site_total_hashes = floatval($site_mo_response['totalHash']); //I'm removing the number format as we need the raw data.
      $site_valid_shares = floatval($site_mo_response['validShares']); //I'm removing the number format as we need the raw data.
      $site_hash_per_second = number_format(intval($site_mo_response['hash2'])); //We already know site total hashes.
      $site_hash_per_second = ' ' . $site_hash_per_second . ' H/s';
    }
    else
    {
      $site_hash_per_second = '';
      $site_total_hashes = 0;
      $site_valid_shares = 0; //Moving to the share system as better to predict payments.
    }
  }

  //Let's get the price of XMR now if we can:

  $current_xmr_price = vyps_mo_xmr_usd_api();

  $mo_array_server_response = array(
      'site_hashes' => $site_total_hashes,
      'site_hash_per_second' => $site_hash_per_second,
      'site_validShares' => $site_valid_shares,
      'current_XMRprice' => $current_xmr_price,
  );

  echo json_encode($mo_array_server_response); //Proper method to return json

  wp_die(); // this is required to terminate immediately and return a proper response
}
