<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/***   This is the new functionalized add points function   ***/
/*** Specfically to make my life easier and 3rd party hooks ***/

function vyps_add_func( $atts )
{
	//NOTE: If there are shortcodes they go in, but generally we will see.
	//Adds and subs will be their own beasts I suppose and will call the PE atts for now
	//NOTE: Removing the other options that are for PE like the days, and DS stuff which does not register to the point log
	$atts = shortcode_atts(
		array(
				'outputid' => 0,
				'outputamount' => 0,
        'refer' => 0,
				'to_user_id' => 0,
        'comment' => '',
    		'reason' => '',
				'vyps_meta_id' => '',
				'vyps_meta_data' => '',
    ), $atts, 'vyps-pe' );

	//NOTE: Only adding. So do not need any input variables
	//ALL THESE SHOULD BE INTS as VYPS is INT only!
	$add_point_id = intval($atts['outputid']);
	$add_amount = intval($atts['outputamount']);
	$to_user_id = sanitize_text_field($atts['to_user_id']);
	$refer_rate = intval($atts['refer']); 	//Refer rate

	//The Reason and meta Meta which should be somewhat text
	$reason = sanitize_text_field($atts['reason']); //Feed into when mining or otherwise. For some reason I did not add the function into mining until recently.
	$vyps_meta_data = sanitize_text_field($atts['vyps_meta_data']);
	$vyps_meta_id = sanitize_text_field($atts['vyps_meta_id']);

	//NOTE: It dawned me I should check to see if point exists. Luckily I made a function that can Tell
	$point_id = $add_point_id;
  if ( empty(vyps_point_name_func($point_id)))
	{
				return 0; //Need better error, but in theory if point does not work, it ceases. Also if they need to name the point.
	}

	//If this is not a transfer or a direct call, then we going to assume its the current id
	if ( $to_user_id == 0 )
	{
		$user_id = get_current_user_id();
	}
	else
	{
		//Ok, if its not 0 then we know its something else
		//However, I'm thinking you should check to see if they are a user so they don't blow stuff up
		$user_info = get_userdata( $to_user_id );

		if ( empty($user_info->user_login) )
		{
			// I guess we have some problems if the user login is empty. We got some problems
			return 0; //I suppose have to figure that out later what to put as an error later on TODO
		}
		else
		{
			//I guess we ok then to move on.
			$user_id = $to_user_id;
		}
	} //End of that $to_user_if

	//At this point I feel optimized $WPDB since there is one return out in case error

	//$WPDB calls
	global $wpdb;
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_points = $wpdb->prefix . 'vyps_points'; //I'm debating if we needed this but I realize I should check at some point if point actually array_key_exists

	//NOTE: I'm putting in a hook for referrals here. I am abivilent about doing a referal function ad itself, this should be all inclusive.
	//If referral rate is fed, and there is a referral, it shoudl just add with it. Double tap.
	//Luckily, I already made functions for this to see if there is a refer id
	if ( $refer_rate > 0)
	{
		//I'm callign the function only if the refer rate is above zero.
		$refer_id = vyps_current_refer_func($user_id);
	}
	else
	{
		//I'm just going to set to zero. I had the option to use a nested if but a variable had to be defined twice. Perhaps I'm micro-optimizing for no good purpose
		$refer_id = 0;
	}

	if ( $refer_id > 0 )
	{
		//I realized these should be labeled what it will go into rather than what it came from
		$meta_id = 'refer';
		$vyps_meta_subid1 = $refer_id; //The User_id goes here
		//I would have liked to have some other way but the inserts have to be different
		$refer_reason = 'refer';
		$refer_amount = doubleval($add_amount); //Why do I do a doubleval here again? I think it was something with Wordfence.
		$refer_amount = intval($refer_amount * ( $refer_rate / 100 )); //Yeah we make a decimal of the $refer_rate and then smash it into the $amount and cram it back into an int. To hell with your rounding.

		//I realized we can go ahead and save an if and insert the refer before the primary
		$data = [
				'reason' => $refer_reason,
				'point_id' => $add_point_id,
				'points_amount' => $refer_amount,
				'user_id' => $refer_id,
				'vyps_meta_id' => $meta_id,
				'vyps_meta_data' => $vyps_meta_data,
				'vyps_meta_subid1' => $user_id,
				'time' => date('Y-m-d H:i:s')
		];
		$wpdb->insert($table_name_log, $data);

		}
		else
		{
			//If no referall, meta_id should remain blank for all intents and purposes
			//And nothing will get inserted
			$meta_id = $vyps_meta_id;
		}

	//Code to do this insert. We are going to assume that adds only add rather than check to see if we need to add.
	//I'm going out on a limb and assume that whoever (including me) is going to check with deduct first if they want to do that. It could be they are just handing points out like Ophrah with cars

	//For primary user
	$data = [
			'reason' => $reason,
			'point_id' => $add_point_id,
			'points_amount' => $add_amount,
			'user_id' => $user_id,
			'vyps_meta_id' => $meta_id,
			'vyps_meta_data' => $vyps_meta_data,
			'time' => date('Y-m-d H:i:s')
	];
	$wpdb->insert($table_name_log, $data);

	//Out it goes! Return 1 for sucess. I suppose it failed, but not sure how we sould inform on that.
	return 1;
}
