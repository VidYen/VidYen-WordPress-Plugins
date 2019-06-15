<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
** More modern version of the credit function
** NOTE: I had to add the meta_subids in to make compatible with new tr and exchange
** Deduct susually doens't need those. *shrugs*
*/

function vyps_point_credit_func($point_id, $point_amount, $user_id, $reason, $vyps_meta_id = '', $vyps_meta_data = '', $vyps_meta_subid1 = '', $vyps_meta_subid2 ='', $vyps_meta_subid3= '', $game_id = '')
{
	//$WPDB calls
	global $wpdb;
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_points = $wpdb->prefix . 'vyps_points'; //I'm debating if we needed this but I realize I should check at some point if point actually array_key_exists

	//Its possible that this could be called without user being logged in but we should still sanitize
	$point_id = intval($point_id);
	$point_amount = abs(intval($point_amount)); //there is an abs() in there but should be using deduct to remove
	$user_id = intval($user_id);
	$reason = sanitize_text_field($reason);
	$vyps_meta_id = sanitize_text_field($vyps_meta_id);
	$vyps_meta_data = sanitize_text_field($vyps_meta_data);
	$vyps_meta_subid1 = sanitize_text_field($vyps_meta_subid1);
	$vyps_meta_subid2 = sanitize_text_field($vyps_meta_subid2);
	$vyps_meta_subid3 = sanitize_text_field($vyps_meta_subid3);
	$game_id = sanitize_text_field($game_id);

	//Deduction for first point id
	$data = [
			'point_id' => $point_id,
			'points_amount' => $point_amount, //I shall fix this one day to point_amount
			'user_id' => $user_id,
			'reason' => $reason,
			'vyps_meta_id' => $vyps_meta_id,
			'vyps_meta_data' => $vyps_meta_data,
			'vyps_meta_subid1' => $vyps_meta_subid1,
			'vyps_meta_subid2' => $vyps_meta_subid2,
			'vyps_meta_subid3' => $vyps_meta_subid3,
			'game_id' => $game_id,
			'time' => date('Y-m-d H:i:s')
	];
	$wpdb->insert($table_name_log, $data);

	//Out it goes! Return 1 for sucess. I suppose it failed, but not sure how we sould inform on that.
	return 1;
}
