<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/***   This is the new functionalized deduct points function   ***/
/*** Specfically to make my life easier and 3rd party hooks ***/

function vyps_deduct_func( $atts ) {

	//Make sure user is logged in.
	if ( is_user_logged_in() ) {

		//Nothing

	} else {

		//If user is not logged in. Code needs to cease. I said CEASE!
		return;

	}

	//NOTE: If there are shortcodes they go in, but generally we will see.
	//Adds and subs will be their own beasts I suppose and will call the PE atts for now
	//NOTE: Removing the adds part
	$atts = shortcode_atts(
		array(
				'firstid' => '0',
				'secondid' => '0',
				'firstamount' => '0',
				'secondamount' => '0',
				'to_user_id' => 0,
        'comment' => '',
    		'reason' => '',
				'btn_name' => '',
    ), $atts, 'vyps-pe' );

	//NOTE: Only adding. So do not need any input variables
	$first_point_id = intval($atts['firstid']);
	$second_point_id = intval($atts['secondid']);
	$first_amount = intval($atts['firstamount']);
	$second_amount = intval($atts['firstamount']);
	$to_user_id = intval($atts['to_user_id']);

	//This should be fed in by the hook doing the call now. ie Mining, Adscend, PE, etc
	$reason = sanitize_text_field($atts['reason']);

	//Button Name. NOTE: This just not get passed by the shortcode, but rather PE
	$btn_name = $atts['btn_name']; //TODO like add... Needs to be looked into

	//NOTE: It dawned me I should check to see if point exists. Luckily I made a function that can Tell
	$pointID = $first_point_id;

	if ( empty(vyps_point_name_func($pointID)) ){

				return 0; //Need better error, but in theory if point does not work, it ceases. Also if they need to name the point.

	} elseif ( $second_point_id != 0 ) {

			//Testing to see if second point id since called for exists.
			$pointID = $second_point_id;
			if ( empty(vyps_point_name_func($pointID)) ){

				return 0; //Well this is empty as well so done for. I should work on this better TODO

			} //End second point


	} //End all point checks to see if exists.


	//If this is not a transfer or a direct call, then we going to assume its the current id
	if ( $to_user_id == 0 ){

		$user_id = get_current_user_id();

	} else {

		//Ok, if its not 0 then we know its something else
		//However, I'm thinking you should check to see if they are a user so they don't blow stuff up
		$user_info = get_userdata( $to_user_id );

		if ( empty($user_info->user_login) ){

			// I guess we have some problems if the user login is empty. We got some problems
			return 0; //I suppose have to figure that out later what to put as an error later on TODO

		} else {

			//I guess we ok then to move on.
			$user_id = $to_user_id;

		} // End of the above else.

	} //End of that $to_user_if

	//At this point I feel optimized $WPDB since there is one return out in case error

	//$WPDB calls
	global $wpdb;
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_points = $wpdb->prefix . 'vyps_points'; //I'm debating if we needed this but I realize I should check at some point if point actually array_key_exists

	//BTN name as there are no referrals in deduct.
	$meta_id = $btn_name;

	//NOTE: Going to check if they have both the amount of first point and second point.
	//Lucky for me I created a balance function. The balance uses pid. Probaly not a great idea, but let's roll with interface

	//set the first id and then check it
	$atts['pid'] = $first_point_id;
	$atts['raw'] = TRUE; //Icon needs to be 0 to get a pure numeric value. I should fix this someday.
	$first_balance = intval(vyps_balance_func($atts));

	//Define the short amounts. Should both be 0 to start.
	$first_short_amount = 0;
	$second_short_amount = 0;

	//See if we have less points than is needed in balance
	if ( $first_balance < $first_amount ) {

		//Note we need to check both and return them. This is to see if we are short points.
		$first_short_amount = $first_amount - $first_balance;

	}

	//Now we got into check if there is any second points

	if ($second_point_id > 0) {
		//set the first id and then check it
		$atts['pid'] = $second_point_id;
		$atts['icon'] = 0; //Icon needs to be 0 to get a pure numeric value. I should fix this someday.
		$second_balance = vyps_balance_func($atts);

		//Check to see if it's over the top. Its possible the first one is under and second is fine
		if  ( $second_balance < $first_amount ) {

			//Note we need to check both and return them. This is to see if we are short points.
			$second_short_amount = $second_amount - $second_balance;

		} //First balance check
	} //End if second point is > 0

	//Simple check to see if either short amount is greater than 0
	if ( $first_short_amount > 0 OR $second_short_amount > 0) {

		return 0; //I'm going to leave the other checks in place but for now this should be enough.

	}

	//NOTE: Deducts need to be negative. Not, I sort of soft checked for negative amounts in the PE with abs()
	$deduct_first_amount = abs($first_amount) * -1;

	//Deduction for first point id
	$data = [
			'reason' => $reason,
			'point_id' => $first_point_id,
			'points_amount' => $deduct_first_amount,
			'user_id' => $user_id,
			'vyps_meta_id' => $meta_id,
			'time' => date('Y-m-d H:i:s')
	];
	$wpdb->insert($table_name_log, $data);

	//NOTE: Second amount deducts. If greater than zero it means its exists.
	if ($second_point_id > 0){

		$deduct_second_amount = abs($second_amount) * -1;

		//Deduction for second point id. TODO I should make it programatic so can have ad naseum 3, 4, and 5 point deducts, but down the road.
		$data = [
				'reason' => $reason,
				'point_id' => $second_point_id,
				'points_amount' => $deduct_second_amount,
				'user_id' => $user_id,
				'vyps_meta_id' => $meta_id,
				'time' => date('Y-m-d H:i:s')
		];
		$wpdb->insert($table_name_log, $data);

	}

	//Out it goes! Return 1 for sucess. I suppose it failed, but not sure how we sould inform on that.
	return 1;

}
