<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** adgate short code to make a postback page ***/

//NOTE: As much as I hate post backs, its not hard to do and adgate doesn't have the Adscend point api tracking system (nor like Coinhive)
//Of course since the adgate site won't have a wp login, has to be just just a shortcode with page. And you will have to wait on adgate to talk to your server
//Lots of terrible things can will go wrong, but the demand for this (due to Adscend just being.... well Adscend) keeps happening so I broke down and decided to do this
//regardless of having to use a post back. I will have to do it in a way that is secure etc etc.

function vidyen_mmo_postback_deduct_func( $atts )
{
	//NOTE: The admin needs to set the post back correctly. We will have no idea what the user id will be as it will be fed into the system by the post back
	//We will need the secret
	//Also NOTE: I changed pid to outputid because i think going forward pid is a bit nondescriptive

	//THis needs to called to get the api key.
	$vidyen_api = sanitize_text_field(vyps_mmo_sql_api_key_func());
	$atts = shortcode_atts(
		array(
				'apikey' => $vidyen_api,
				'profile' => '',
				'ip1' => '163.182.175.208',
				'ip2' => '163.182.175.208',
				'ip3' => '208.253.87.210',
				'point_id' => 0,
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

	$site_vidyen_api = $atts['apikey'];

	//define('AdGate_IP', $postback_ip_address); // Note: as noted above change the IP to match what is in your affiliate panel.
	$post_ip = $_SERVER['REMOTE_ADDR'];

	if (isset($_POST['apikey']))
	{
			if($site_vidyen_api !=  $_POST['apikey'])
			{
				// Throw either a custom Exception or just throw a generic \Exception
			 //header('HTTP/1.1 203 Partial Information');
			 //exit(); //NOTE: I put exit as the AdGate method was bad
			 return 'Invalid API key';
			}
	}
	else
	{
		return 'Api Key not Set!!';
	}

	if (!isset($_POST['email']))
	{
		return 'Email not set!';
	}
	elseif (!isset($_POST['points']))
	{
		return 'Point value not set!';
	}

	//We are getting the user and then get the user id from that since they might be different between servers. I'm just guessing
	if ( isset($_POST['email']) AND isset($_POST['points']))
	{
		$user_email = sanitize_email($_POST['email']); //Huh they actualyly had this. Hrm.... honestly it doesn't seem to care about the email in the get. Learn something every day.
		$user_data = get_user_by('email', $user_email);
		$user_id = $user_data->ID;
		//$user_id = 2;

		$points = intval($_POST['points']);

		if (intval($atts['point_id']) != 0)
		{
			$point_id = intval($atts['point_id']);
		}
		else
		{
			$point_id = intval(vyps_mmo_sql_point_id_func()); //give the option, for shortcodes. the api will be universal.
		}

		$point_amount = intval($points);
		$reason = sanitize_text_field($atts['reason']);

		$vyps_meta_id = 'mmo'  . $user_id . $transactionId_sanitized; //the meta_id will be adgate with userid plus the transaction id. To see if its unique.

		$current_balance = vyps_point_balance_func($point_id, $user_id); //need to check to see if they have an actual balance to report //NOTE: I opted with letting the other site tell how much it will withdraw at a time.

		if( $current_balance >= $point_amount) // action = 1 CREDITED // action = 0 charge back
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

	return "Unknown error!";
}

/* Telling WP to use function for shortcode */
add_shortcode( 'vidyen-mmo-deduct', 'vidyen_mmo_postback_deduct_func');
