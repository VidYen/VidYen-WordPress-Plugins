<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** adgate short code to make a postback page ***/

//NOTE: As much as I hate post backs, its not hard to do and adgate doesn't have the Adscend point api tracking system (nor like Coinhive)
//Of course since the adgate site won't have a wp login, has to be just just a shortcode with page. And you will have to wait on adgate to talk to your server
//Lots of terrible things can will go wrong, but the demand for this (due to Adscend just being.... well Adscend) keeps happening so I broke down and decided to do this
//regardless of having to use a post back. I will have to do it in a way that is secure etc etc.

function vidyen_mmo_postback_func( $atts )
{
	//NOTE: The admin needs to set the post back correctly. We will have no idea what the user id will be as it will be fed into the system by the post back
	//We will need the secret
	//Also NOTE: I changed pid to outputid because i think going forward pid is a bit nondescriptive

	//THis needs to called to get the api key.
	$api_key = sanitize_text_field(vyps_mmo_sql_api_key_func());
	$atts = shortcode_atts(
		array(
				'apikey' => $api_key,
				'profile' => '',
				'ip1' => '163.182.175.208	',
				'ip2' => '163.182.175.208	',
				'ip3' => '208.253.87.210',
				'outputid' => 0,
				'outputamount' => 0,
				'refer' => 0,
				'to_user_id' => 0,
				'comment' => '',
				'reason' => 'MMO Transfer',
				'meta_id' => '',
				'round' => 'default',
				'pro'=> FALSE,
		), $atts, 'vyps-adgate' );

	$round_direction_decision = $atts['round']; //By default this is default, which just takes the direction its closest too.

	//The scarcy thing is... This post back can be writing to your SQL tables. SO we HAVE to be careful with it.
	global $wpdb;
	$table_name_log = $wpdb->prefix . 'vyps_points_log';

	//NOTE: Due to the lax nature of AdGate security methods. I am adding my own API system.
	//EX: https://vidyen.com/fabius/adgate-postback/?tx_id={transaction_id}&user_id={s1}&point_value={points}&usd_value={payout}&offer_title={vc_titlpoe}&point_value={points}&status={status}&api=7xB944
	//The api=7xB944 has to be the same on both your shortcode and post back. Its not required, but if you set a shortcode for it. Then it has to have it.
	$site_api_key = $atts['apikey'];

	//Copied and pasted from https://github.com/adgatemedia/adgaterewards/blob/master/postback_pdo_example.php
	//Modified to deal with my format and OCD
	/**
	 * For a plain PHP page to receive the postback data from AdGate Media you may simply
	 * retrieve the array from the global $_GET variable. To ensure that the data is coming
	 * from AdGate Media check that the server sending the data is from AdGate Media by the ip
	 * address as listed on your affiliate panel at http://adgatemedia.com under
	 * the Postbacks Section and the Postback Information heading.
	 */
	//define('AdGate_IP', $postback_ip_address); // Note: as noted above change the IP to match what is in your affiliate panel.
	$post_ip = $_SERVER['REMOTE_ADDR'];
	//$data = null; //Don'te need this.
		/**
	 * Check the Remote Address is AdGate Media
	 * if it is not throw an Exception
	 */

	//NOTE: Checking to make sure the post back ips match and if there is a user api key then check that.
	if(in_array($post_ip, $atts)) //Some old greygoose and bad coding. I'm just checking to see if the ip address exists in shortcode.
	{
		if (isset($_GET['api_key']))
		{
				if($site_api_key !=  $_GET['api_key'])
				{
					// Throw either a custom Exception or just throw a generic \Exception
				 //header('HTTP/1.1 203 Partial Information');
				 //exit(); //NOTE: I put exit as the AdGate method was bad
				 return 'Invalid API key';
				}
		}
	}
	else
	{
	    // Throw either a custom Exception or just throw a generic \Exception
			//header('HTTP/1.1 203 Partial Information');
	    //exit(); //NOTE: I put exit as the AdGate method was bad.
			return 'Invalid IP address.';
	}

	//We are getting the email and then get the user id from that since they might be different between servers. I'm just guessing
	if ( isset($_GET['email']) AND isset($_GET['point_value']) AND isset($_GET['status']) AND isset($_GET['tx_id']))
	{
		$user_email = sanitize_email($_GET['email']); //Huh they actualyly had this. Hrm.... honestly it doesn't seem to care about the email in the get. Learn something every day.
		$user_data = get_user_by('email', $user_email);
		$user_id = $user_data->ID;
		//$user_id = 2;

		$points = isset($_GET['point_value']) ? $_GET['point_value'] : null;
		$action = isset($_GET['status']) ? $_GET['status'] : null; //Determines if added (1) or subtracted (0) NOTE: This is different than Adgate where 2 is a chargeback
		$tx_id = isset($_GET['tx_id']) ? $_GET['tx_id'] : null; //This will be EPOCH time stamp being fed so yeah
		//$ipuser = isset($_GET['ip']) ? $_GET['ip'] : null; //Note used or needed.

		//NOTE: Ok we got that post back. And if the keys match in theory we have the variables above. But there is no hell in way I'm trusting adgate to SQL the users Database with that data
		//Yeah its unlikely adgate may try an SQL injection their user base, but if the user is lax with their secret key and someone knows what this is, they can have an injection fest
		$userId_sanitized = intval($userId); //User Id should be an int
		$transactionId_sanitized = sanitize_text_field($transactionId); //This actually doesn't have to be collected but could be useful in one of the metas columsn
		$action_sanitized = intval($action); //Good thing I read the documentation. According to adgate, if this is 1 there should be a reward and 2 if there is punishment for some reason. Should be int

		$point_id = intval(vyps_mmo_sql_point_id_func()); //this is set by the wpdb so only one point at a time.
		$point_amount = intval($points);
		$reason = sanitize_text_field($atts['reason']);

		$vyps_meta_id = 'mmo'  . $userId_sanitized . $transactionId_sanitized; //the meta_id will be adgate with userid plus the transaction id. To see if its unique.

		$current_balance = vyps_point_balance_func($point_id, $user_id); //need to check to see if they have an actual balance to report //NOTE: I opted with letting the other site tell how much it will withdraw at a time.
		
		if($action == 0 AND $current_balance >= $point_amount) // action = 1 CREDITED // action = 0 charge back
		{
				return vyps_point_deduct_func( $point_id, $point_amount, $user_id, $reason, $vyps_meta_id ); //I knew I had a good reason to use this
				//The above should resturn a 1 if successful. I'm not going to add an add here just yet. This is an output system.
				//If the get gets a 1 then it adds the points on the other side. I would recommend not doing an all system just like 100 points.
				//I am going to add a balance api, but may not be needed.
		}
		else
		{
			return 0; //simple enough. It didn't work. Did not add points.
		}

		//The rest of the post back isn't needed. I will delete but will make a different page for ads or balances.
	}

	return "Invalid postback URL!";
}

/* Telling WP to use function for shortcode */
add_shortcode( 'vidyen-mmo-postback', 'vidyen_mmo_postback_func');
