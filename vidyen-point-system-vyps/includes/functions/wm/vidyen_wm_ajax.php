<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// For the 3.0 update, I've decided to use a system where it just cashes out every 100,000 hashes
// I need to set the field for hash per point and then a multi-but lets go with it
// -Felty

/*** AJAX TO GRAB HASH PER SECOND FROM MO ***/

// register the ajax action for authenticated users
add_action('wp_ajax_vidyen_wm_api_action', 'vidyen_wm_api_action'); //Commented out as you have to get the  no prov

//register the ajax for non authenticated users
//NOTE: I need to get feed back if nonautheneticaed users should actually be able to use this.
add_action( 'wp_ajax_nopriv_vidyen_wm_api_action', 'vidyen_wm_api_action' );

// handle the ajax request
function vidyen_wm_api_action()
{
  global $wpdb; // this is how you get access to the database

  //NOTE: I do not think there is a need for nonce as no user input to wordpress

  //Post gather from the AJAX post
  $site_wallet = sanitize_text_field($_POST['site_wallet']);
  $site_worker = sanitize_text_field($_POST['site_worker']);

  $user_id_explode = explode("-", $site_worker); //It's possible that users will have - somewhere else but first shall do
  $user_id = intval($user_id_explode[0]);

  /*** MoneroOcean Gets***/
  //Site get
  $site_url = 'https://api.moneroocean.stream/miner/' . $site_wallet . '/stats/' . $site_worker;
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

  //Let's get the price of XMR now if we can: I need to really do something with this since I put the effort into finding this out.
  $current_xmr_price = vyps_mo_xmr_usd_api();

  $key = 'vidyen_wm_total_hash';
  $single = TRUE;
  $user_prior_total_hashes = floatval(get_user_meta( $user_id, $key, $single ));

  //Get time stamp
  $date_key = 'vidyen_wm_last_mined_date';
  $user_prior_mined_date = floatval(get_user_meta( $user_id, $date_key, $single )); //This will be raw UTC
  $current_mined_date = time();
  $time_difference = $current_mined_date - $user_prior_mined_date;

  //Goign to credit it here. Dev NOTE: I believe that if MO reports wrong there is nothing we can do.
  if(  $site_total_hashes > $user_prior_total_hashes )
  {
    $rewarded_hashes = $site_total_hashes - $user_prior_total_hashes; //Find the different
    $credit_result = intval($rewarded_hashes / $hash_per_point);
    $meta_value = $site_total_hashes;
    update_user_meta( $user_id, $key, $meta_value, $prev_value );
    update_user_meta( $user_id, $date_key, $current_mined_date, $prev_value );
  }
  elseif( $time_difference > 86400 )
  {
    //Ok some developer notes about the above logica
    //This should only fire if the total hashes are not greater than last reported
    //Under normal circumstations they have never mined or worker has been deleted because longer than 24 hours
    //However, it is possible that the MO api is down ergo the worker will come back with the right Hashes
    //As that often the mining pool will still be counting hashes but unlikley that this is ongoing beyond 24 hours.
    //I am trying to visualize a case where the miner mined and then the server crashed or the worker did not get deleted
    //Eventually the above will be positive when worker comes back online (I think. -Felty)
    $meta_value = 0;
    update_user_meta( $user_id, $key, $meta_value, $prev_value );
  }

  $mo_array_server_response = array(
      'site_hashes' => $site_total_hashes,
      'site_hash_per_second' => $site_hash_per_second,
      'site_validShares' => $site_valid_shares,
      'current_XMRprice' => $current_xmr_price,
  );

  echo json_encode($mo_array_server_response); //Proper method to return json

  wp_die(); // this is required to terminate immediately and return a proper response
}
