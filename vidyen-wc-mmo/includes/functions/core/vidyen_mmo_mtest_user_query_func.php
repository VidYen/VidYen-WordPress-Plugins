<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//This is designed for the user query in the meta.
//Perhaps the name is too long?
function vidyen_mmo_mtest_user_query_func($mtest_user_id)
{
	$user_query = new WP_User_Query( array( 'meta_key' => 'vidyen_mmo_mtest_id', 'meta_value' => $mtest_user_id ) );

	$users = $user_query->get_results();

	//Thi is a bit hackey. Basically it does a for loop and stop on the first one otherwise if none found returns a 0.
	if ( ! empty( $users ) )
	{
	    foreach ( $users as $user )
			{
	        // get all the user's data
	        $user_info = get_userdata( $user->ID );
	        $output_result = $user_info->ID;
					return intval($output_result);
	    }
		}
		else
		{
	    return 0;
		}
		//print_r($users[0]);
	return;

	//return 'There should be something here: '. $users[0];
}
