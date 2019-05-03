<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//NOTE: This is to allow users to register their id from game to site

//Responses
//-1 unknown error
//0 = default, no transact
//1 = success
//2 = no user id found on

function vidyen_mmo_postback_register_func( $atts )
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

	//We are getting the user and then get the user id from that since they might be different between servers. I'm just guessing
	//Need email an duser id
	if ( (isset($_POST['email']) AND isset($_POST['userid'])) )
	{
		//Find the users who has the email (should only be one)
		$user_email = sanitize_email($_POST['email']); //Huh they actualyly had this. Hrm.... honestly it doesn't seem to care about the email in the get. Learn something every day.
		$user_data = get_user_by('email', $user_email);
		$user_id = $user_data->ID;

		//check to see if the email is found
		if ($user_id < 1)
		{
			return 2; //a 2 means email not found
		}

		//Ok now we know the user is on site.
		$loa_user_id = sanitize_text_field($_POST['userid']);
		$loa_id_check = intval(vidyen_mmo_loa_user_query_func($loa_user_id));
		//$user_id = 1; //Hard coded for now
		if ($loa_id_check > 1)
		{
			return 0; //Id was already found
		}

		//This is hardcoded, but the label we are going to cram into the usermeta table
		$key = 'vidyen_mmo_loa_id';
		//Ok now we just register it.

		update_user_meta( $user_id, $key, $loa_user_id );

		return 1; //sucess.

	}


	return -1; //Uknown reason
}

/* Telling WP to use function for shortcode */
add_shortcode( 'vidyen-rts-register', 'vidyen_mmo_postback_register_func');
