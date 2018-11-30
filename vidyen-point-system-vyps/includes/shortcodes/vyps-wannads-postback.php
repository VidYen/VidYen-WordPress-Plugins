<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** Wannads short code to make a postback page ***/

//NOTE: As much as I hate post backs, its not hard to do and Wannads doesn't have the Adscend point api tracking system (nor like Coinhive)
//Of course since the wannads site won't have a wp login, has to be just just a shortcode with page. And you will have to wait on Wannads to talk to your server
//Lots of terrible things can will go wrong, but the demand for this (due to Adscend just being.... well Adscend) keeps happening so I broke down and decided to do this
//regardless of having to use a post back. I will have to do it in a way that is secure etc etc.

function vyps_wannads_postback_func( $atts )
{

	//OK. Need people to get referrals:

	if (vyps_wannads_pro_check_func() <> 1)
	{

		return 'Referral code not setup or need Wannads Pro version installed for Post Back feature. Please see <a href="https://vidyen.com/wannads-install/">VidYen Store</a> for options.';

	}

	//The scarcy thing is... This post back can be writing to your SQL tables. SO we HAVE to be careful with it.
	global $wpdb;
	$table_name_log = $wpdb->prefix . 'vyps_points_log';

	//NOTE: The admin needs to set the post back correctly. We will have no idea what the user id will be as it will be fed into the system by the post back
	//We will need the secretkey
	//Also NOTE: I changed pid to outputid because i think going forward pid is a bit nondescriptive

	$atts = shortcode_atts(
		array(
				'apikey' => '',
				'profile' => '0',
				'secretkey' => '',
				'outputid' => 0,
				'outputamount' => 0,
				'refer' => 0,
				'to_user_id' => 0,
				'comment' => '',
				'reason' => 'Wannads',
				'btn_name' => '',
		), $atts, 'vyps-wannads' );


	//Checking to see if shortcodes were misset (Actually we may only need the secret key)

	if ( $atts['apikey'] == '' )
	{

		return "Wannads API key not set!";

	}

	//This is the point id that the post back should go to. ie. the Point ID
	if ( $atts['outputid'] == 0 )
	{


		return "You did not set output point ID!";

	}

	//Secret key. Gods know you don't want a random person filling up your SQL server with junk.
	if ( $atts['secretkey'] == '' )
	{

		return "You did not set secret API key!";

	}

	//Copied and pasted from https://wannads.readme.io/docs/postback-notifications
	//Modified to deal with my format and OCD
	$secret = $atts['secretkey']; // check your app info at www.wannads.com
	$userId = isset($_GET['subId']) ? $_GET['subId'] : null;
	$transactionId = isset($_GET['transId']) ? $_GET['transId'] : null;
	$points = isset($_GET['reward']) ? $_GET['reward'] : null;
	$signature = isset($_GET['signature']) ? $_GET['signature'] : null;
	$action = isset($_GET['status']) ? $_GET['status'] : null; //Determines if added (1) or subtracted (2)
	$ipuser = isset($_GET['userIp']) ? $_GET['userIp'] : "0.0.0.0";

	// validate signature
	//BTW while coding this I just realized I like the { } after line return as it was easier for even me to read even though i twas my habbit
	//This change will certainly annoy the peopel who go through my code and realize I changed styles at a certain point
	if(md5($userId.$transactionId.$points.$secret) != $signature)
	{
	    $output_message = "ERROR: Signature doesn't match";
	    return $output_message;
	}

	if($action == 2) { // action = 1 CREDITED // action = 2 REVOKED
	    $points = -abs($points);
	}

	//NOTE: Ok we got that post back. And if the keys match in theory we have the variables above. But there is no hell in way I'm trusting Wannads to SQL the users Database with that data
	//Yeah its unlikely Wannads may try an SQL injection their user base, but if the user is lax with their secret key and someone knows what this is, they can have an injection fest
	$userId_sanitized = intval($userId); //User Id should be an int
	$transactionId_sanitized = sanitize_text_field($transactionId); //This actually doesn't have to be collected but could be useful in one of the metas columsn
	$points_sanitized = intval($points); //I actually wonder if this will be a problem as they may use decimals where I have always frowned on it as it confuses non technical users. Sorry not sorry, Satoshi
	$action_sanitized = intval($action); //Good thing I read the documentation. According to wannads, if this is 1 there should be a reward and 2 if there is punishment for some reason. Should be int
	$ipuser_sanitized = sanitize_text_field($ipuser); //Again, not a have to have, but would be useful if admin needs to look at issue

	//OK, now we santized everything. In theory we could use our nice functions.
	//We will need to throw some more stuff on to the shortcode array to feed the add

	$atts['to_user_id'] = $userId_sanitized; //In theory you could set this in shortcode itself but I have no idea why other than debugging. Well that is good enough.
	$atts['outputamount'] = $points_sanitized; //The int of the points.
	$atts['btn_name'] = 'wannads'  . $userId_sanitized . $transactionId_sanitized; //the meta_id will be wannads with userid plus the transaction id. To see if its unique.

	//NOTE: I am about to rage here. This is why postbacks are crap and always will be crap. Why would they be post back crap that is dupblicate. I dunno. This is abusive to the admins server.
	//Anyways... I'll just use the btn_name for the metaId... check if meta id is same and then if it is. Anyways, hopefully this is a good solution to a bad implementation

	if(vyps_meta_check_func($atts) == 1) //Seeing if this return a 1. If so no duplicates.
	{

		//Ok, we are good to process transaction as there are no duplicates.
		if( $action_sanitized == 1) //Reward time. AKA add points
		{

			//We will need to throw some more stuff on to the shortcode array to feed the add
			vyps_add_func( $atts );

		}

		elseif ( $action_sanitized == 2 )
		{

			//Subtract it. It looks like points has been added negative
			vyps_deduct_func( $atts );

		}

	}

	elseif(vyps_meta_check_func($atts) == 2)
	{

	    // If the transaction already exist please echo DUP.
	    return "Duplicate";
	}

	else
	{
		return 'Error:' . vyps_meta_check_func($atts); //Means we got something else than 1 or 2, which probaly means SQL error but who knows. Hopefully the meta check will tell us.
	}

}

/* Telling WP to use function for shortcode */
add_shortcode( 'vyps-wannads-postback', 'vvyps_wannads_postback_func');
