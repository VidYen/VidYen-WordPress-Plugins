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
      $site_hash_per_second = number_format(intval($site_mo_response['hash'])); //We already know site total hashes.
      $site_hash_per_second = ' ' . $site_hash_per_second . ' H/s';

      $_SESSION['attention_total'] = 0; //Do not want to over count if they refresh page.
    }
    else
    {
      $site_hash_per_second = '';
      $site_total_hashes = 0;

      //NOTE if we got an array but not data MO talked to us. which means I guess ok to do the following:
      //Side notes 30 should be reasonable without messing up. Once they get to actual hashes people be less antsy
      if(isset($_SESSION['attention']))
      {
        $_SESSION['attention_total'] = $_SESSION['attention_total'] + $_SESSION['attention'];

        $site_total_hashes = $_SESSION['attention_total'];
      }
    }
  }

  $mo_array_server_response = array(
      'site_hashes' => $site_total_hashes,
      'site_hash_per_second' => $site_hash_per_second,
  );

  echo json_encode($mo_array_server_response); //Proper method to return json

  wp_die(); // this is required to terminate immediately and return a proper response
}
