<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
** More modern version of the deduct function
**
**
*/

function vidyen_mmo_wm_point_deduct_func( $point_id, $point_amount, $game_id, $reason, $vyps_meta_id )
{
	//$WPDB calls
	global $wpdb;
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_points = $wpdb->prefix . 'vyps_points'; //I'm debating if we needed this but I realize I should check at some point if point actually array_key_exists

	//Its possible that this could be called without user being logged in but we should still sanitize
	$point_id = intval($point_id);
	$point_amount = abs(intval($point_amount)) * -1; //Realized that to deduct I need to subtract
	$game_id = sanitize_text_field($game_id);
	$reason = sanitize_text_field($reason);
	$vyps_meta_id = sanitize_text_field($vyps_meta_id);
	$user_id = 0;

	//Deduction for first point id
	$data = [
			'point_id' => $point_id,
			'points_amount' => $point_amount, //I shall fix this one day to point_amount
			'user_id' => $user_id,
			'game_id' => $game_id,
			'vyps_meta_id' => $vyps_meta_id,
			'reason' => $reason,
			'time' => date('Y-m-d H:i:s')
	];
	$wpdb->insert($table_name_log, $data);

	//Out it goes! Return 1 for sucess. I suppose it failed, but not sure how we sould inform on that.
	return 1;
}
