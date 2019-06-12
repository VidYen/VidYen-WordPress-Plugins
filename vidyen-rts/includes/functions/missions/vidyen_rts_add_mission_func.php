<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Copy of the vyps credit function, but use it to add the mesata to the rts log
//My bane of life is that i need to go back and correc it all but time... Time is what I have the least of.

/*
** More modern version of the credit function
**
**
*/

function vidyen_rts_add_mission_func( $mission_id, $mission_time, $user_id, $reason, $vyps_meta_id )
{
	//$WPDB calls
	global $wpdb;
	$table_name_log = $wpdb->prefix . 'vidyen_rts_mission_log';

	//Its possible that this could be called without user being logged in but we should still sanitize
	$mission_id = sanitize_text_field($mission_id); // Mission ids will be text ids... like 'sackvillage05'
	$mission_time = intval($mission_time); //I'm not sure if mission log should have amount but we will see
	$user_id = intval($user_id);
	$reason = sanitize_text_field($reason);
	$vyps_meta_id = 'start'.$mission_id;

	//Insert the SQL!
	$data = [
			'mission_id' => $mission_id,
			'mission_time' => $mission_time, //I shall fix this one day to point_amount
			'user_id' => $user_id,
			'mission_meta_id' => $vyps_meta_id,
			'reason' => $reason,
			'time' => date('Y-m-d H:i:s')
	];
	$wpdb->insert($table_name_log, $data);

	//Out it goes! Return 1 for sucess. I suppose it failed, but not sure how we sould inform on that.
	return 1;
}
