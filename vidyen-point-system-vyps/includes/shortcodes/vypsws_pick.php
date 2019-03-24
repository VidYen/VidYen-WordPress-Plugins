<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Ok. This might be weird. But I'm making a pure function to figure out what the top balances
//To determine a percetage of ownership in a points
//Then determine ownership in mining shares. Like you own 60% of the points you get 60 out of 100 hashes.
//I'm going to feed it like a shortcode. Might be the wrong way about it.
//So the more I think about it. I don't need a rank, just a percent out of hundred.
//Based out of amount of total a user gets a range like 0-0 for no coiners, and 5-20 for someone who owned 15% etc.
//Evenutally, it would be nice to see a percentage. I could reuse the leaderboard and get a percentage pb pretty easily but this is
//is to feed array the user id. Actually this should just straight up determin own and feed it back in to the miners
//HAH I'm good. Well better than most.

//NOTE: I'm keeping as a shortcode so I can just see the returns of the pid. May have to see what the pid for the miner is called.

function vyps_worker_shareholder_pick( $atts ) {

	//Shortcode stuff
	//I'm going to eventually have site admins set logs for activities like reason etc and the meta fields, but for now.
	$atts = shortcode_atts(
		array(
				'pid' => '1',
				'shareholder' => '1',
		), $atts, 'vyps-wsp' );


	//PID set.
	//$point_id = $atts['pid']; //See what I did there. I got the shareholder pid from the vy256 so you can switch who own what pid rather than into mining. So adscend people can get XMR.
	$point_id = $atts['shareholder'];



	global $wpdb;
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_users = $wpdb->prefix . 'users';

	//BTW the number of IDs should always match the number of rows, NO EXCEPTIONS. If it doesn't it means the admin deleted a row
	//And that is against the psuedo-blockchain philosophy. //Also it dawned on me I can rewrite the public log here.

	//need to know how many users there are at this point in time.
	$number_of_users_rows_query = "SELECT max( id ) FROM ". $table_name_users;  //checking to see how many users there are on this wp install. BTW not all users will have points.
	$number_of_users_rows = $wpdb->get_var( $number_of_users_rows_query ); //Ok. I realized that not only prepare() doesn't work it, there is no varialbes needed to sanitize as the table name is actually hard coded.

	//$number_of_log_rows = $wpdb->get_var( "SELECT max( id ) FROM $table_name_log" ); //No WHERE needed. All rows. No exceptions
  $number_of_log_rows_query = "SELECT max( id ) FROM ". $table_name_log;  //I'm wondering if a prepare is even needed, but throw it all in.
  $number_of_log_rows = $wpdb->get_var( $number_of_log_rows_query ); //Ok. I realized that not only prepare() doesn't work it, there is no varialbes needed to sanitize as the table name is actually hard coded.

	//NOTE: need a sum of all points of id. So we can get a percent.
	$total_amount_data_query = "SELECT sum(points_amount) FROM ". $table_name_log . " WHERE point_id = %d";
	$total_amount_data_query_prepared = $wpdb->prepare( $total_amount_data_query, $point_id ); //pulling this from the shortcode atts, by default its 1. Technically it won't work without a coin, but *shrugs*
	$total_amount_data = $wpdb->get_var( $total_amount_data_query_prepared );

	$total_amount_data = intval($total_amount_data); //Need to make it int

	//return "The count is ". $rank_order_array_count . "<br>" . $rank_order_array['0'] . "<br>". $rank_order_array['1'] . "<br>". $rank_order_array['2'] . "<br>" . $rank_order_array['3'] . "<br>". $rank_order_array['4']; //testing this. //requires 4 users or gives error

	//OK. I'm going to go through each of the users and sum them by pointid and
	for ($x_for_count = $number_of_users_rows; $x_for_count >= 0; $x_for_count = $x_for_count - 1 ) { //Let's just use the order array count. How many users could their possibly be?

		//there needs to be a rank() function soemwhere.
		//We do need this.
		//$amount_data = $wpdb->get_var( "SELECT points_amount FROM $table_name_log WHERE id= '$x_for_count'" );
    $amount_data_query = "SELECT sum(points_amount) FROM ". $table_name_log . " WHERE user_id = %d AND point_id = %d";
    $amount_data_query_prepared = $wpdb->prepare( $amount_data_query, $x_for_count, $point_id ); //Note current user id is the x_for_count
    $amount_data = $wpdb->get_var( $amount_data_query_prepared );

		$amount_data = intval($amount_data); //need to set this a int and if it's zero then ignore the output. BTW I should put less than, but I think negative numbers and zeroes have their place

		//OK why don't we do this. Just return the user id if they win against the house each time.
		$user_percentage = $amount_data / $total_amount_data; //This should return a percentage

		//Ok I love math. The idea is to bet a percent like .4 and then * 100 which would make it 40. However, if they own none they get zero as the check number
		$user_range = intval($user_percentage * 100);

		//So we get a random number reach time. Yes its a random for each loop but if its a static number out of the loop then it becomes easier for the users down the list to win.
		//BTW I thought about doing an inf loop, but honestly that could end badly for the server if RNG doesn't want anyone to win. If the winner doesn't happen it goes to house
		$win_check = mt_rand(1,100); //Note it does tno start at zero because we don't want no-pointers getting XMR

		//BTW the idea is if you own 100% you always win the mining share. So...

		if ( $win_check <= $user_range ){

			//User has won, let's move on
			return $x_for_count; //thats all we know

		}


	}

	return 0; //If no one wins the house wins.

}

// Shortcode addition

add_shortcode( 'vyps-wsp', 'vyps_worker_shareholder_pick');
