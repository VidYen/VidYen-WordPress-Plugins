<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//This is designed for the user query in the meta.
//Perhaps the name is too long?
function vidyen_mmo_loa_user_query_func($loa_user_id)
{
	$user_query = new WP_User_Query( array( 'meta_key' => 'vidyen_mmo_loa_id', 'meta_value' => $loa_user_id ) );

	$user_id_output = $user_query[0]; //returns the first person with id. Not good at all but will fix laters

	return $user_id_output;
}
