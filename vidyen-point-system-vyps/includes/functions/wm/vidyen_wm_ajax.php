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

  //First things first. Let's pull the variables with a single SQL call
  $vy_wm_parsed_array = vidyen_vy_wm_settings();
  $index = 1; //Lazy coding but easier to copy and paste stuff.

  //I don't think we need anything beyond this fromt the SQL. Its here if we need it though
  $hash_per_point = $vy_wm_parsed_array[$index]['hash_per_point'];
	$point_id = 	$vy_wm_parsed_array[$index]['point_id'];
  $wm_pro_active = $vy_wm_parsed_array[$index]['wm_pro_active']; //Whoops yeah need this
  $wm_woo_active = $vy_wm_parsed_array[$index]['wm_woo_active']; //And these
  $discord_webhook = $vy_wm_parsed_array[$index]['discord_webhook'];
  $discord_text = $vy_wm_parsed_array[$index]['discord_text'];

  //Post gather from the AJAX post
  $site_wallet = sanitize_text_field($_POST['site_wallet']);
  $site_worker = sanitize_text_field($_POST['site_worker']);

  $user_id_explode = explode("-", $site_worker); //It's possible that users will have - somewhere else but first shall do
  $user_id = intval($user_id_explode[0]);

  //I am double checking for shennanigans with users getting credit for things not of their own.
  if ($user_id != get_current_user_id())
  {
      wp_die(); // this is required to terminate immediately and return a proper response
  }

  //Init variables in case not called
  $site_total_hashes = 0;
  $credit_result = 0;
  $rewarded_hashes = 0;
  $current_xmr_price =0;

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

    $reason = 'WebMining'; //Honestly, I should create a global reason variable but I have deadlines.

    //Ok going to check for pro and woo mode.
    if($wm_pro_active == 1 AND $wm_woo_active == 1)
    {
      $credit_result = vyps_ww_point_credit_func( $user_id, $credit_result, $reason ); //Note no point ID's
    }
    else
    {
      //The credit result will now be pushed to the vyps credit.
      $credit_result = vyps_point_credit_func($point_id, $credit_result, $user_id, $reason);
    }

    if($wm_pro_active == 1 AND $discord_webhook != '')
    {
      $username = 'Reward Report Bot'; //I need to fix this. Gah!

      //if you can use a discord hook you can learn how to type it in lower case.
      //User name replace.
      $discord_text = str_replace("[user]",vidyen_user_display_name($user_id),$discord_text);

      //Amount replace.
      $discord_text = str_replace("[amount]",$credit_result,$discord_text);

      $discord_result = vidyen_discord_webhook_func($discord_text, $username, $discord_webhook);
    }

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
      'credit_result' => $credit_result
      'rewarded_hashes' => $rewarded_hashes,
      'current_XMRprice' => $current_xmr_price,
  );

  echo json_encode($mo_array_server_response); //Proper method to return json

  wp_die(); // this is required to terminate immediately and return a proper response
}
