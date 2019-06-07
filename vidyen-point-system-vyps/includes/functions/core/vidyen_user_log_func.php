<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//This is a complete new user log. I'm going to do advance SQL stuff as much as I beat on the old version, it wouldn't work the way I wanted.

/* Main Public Log shortcode function */

function vidyen_user_log_func($atts)
{

	//Shortcode stuff
	//I'm going to eventually have site admins set logs for activities like reason etc and the meta fields, but for now.
	$atts = shortcode_atts(
		array(
				'point_id' => 0,
				'reason' => '',
				'rows' => 50,
				'bootstrap' => FALSE,
				'pages' => 10, //How many pages will have
				'start' => 1,
				'end' => 5,
		), $atts, 'vidyen-user-log' );

	$point_id = $atts['point_id'];
	$reason = $atts['reason'];
	$table_row_limit = $atts['rows']; //50 by default
	$boostrap_on = $atts['bootstrap'];
	$max_pages = $atts['pages'];
	$max_pages_middle = intval($max_pages/2); //The middle in theory. I guess?

	//Start and end row
	$start_row = $atts['start'];
	$end_row = $atts['end'];

	//This is obvious
	$user_id = get_current_user_id(); //Over riding the current userid to show just the current user. I have no idea if this actually works as may have not set it up correctly.

	//SQL setup stuff
	global $wpdb;
	$table_name_points = $wpdb->prefix . 'vyps_points';
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_users = $wpdb->prefix . 'users'; //Needed for their name.

	//SQL query of current user
	$user_data_query = "SELECT * FROM ". $table_name_log . " WHERE user_id = %d";
	$user_data_query_prepared = $wpdb->prepare( $user_data_query, $user_id );
	$user_data = $wpdb->get_results( $user_data_query_prepared );

	/*
	$result = $wpdb->get_results ( "
    SELECT *
    FROM  $wpdb->posts
        WHERE post_type = 'page'
	" );

	foreach ( $result as $page )
	{
	   echo $page->ID.'<br/>';
	   echo $page->post_title.'<br/>';
	}
	*/

	//Headers
	$transaction_id_label = "Transaction ID";
	$date_label = "Date";
	$display_name_label = "Display Name";
	$point_type_label = "Point Type";
	$point_id_label = "PID";
	$amount_label = "Amount";
	$reason_label = "Adjustment Reason";

	//$html_output = 'Begin<br><br>';
	$html_output = '<table width="100%">';
	$html_output .= "
			<tr>
				<th>$transaction_id_label</th>
				<th>$date_label</th>
				<th>$display_name_label</th>
				<th>$point_type_label</th>
				<th>$amount_label</th>
				<th>$reason_label</th>
			</tr>
	";

	$index = 0;

	//The variable $result is just arbitrarty and could have been anything but was a resutl dump of user data.
	foreach ($user_data as $result)
	{
		$transaction_id = $result->id;
		$user_id = $result->user_id;
		$reason = $result->reason;
		$transaction_time = $result->time;
		$point_id = $result->point_id;
		$point_amount = $result->points_amount;

		//Array parsing to cram it into multi dimensional row
		//TODO: Add index names and not numbers for second part!
		$parsed_array[$index][0] = $index;
		$parsed_array[$index][1] = $transaction_id;
		$parsed_array[$index][2] = $user_id;
		$parsed_array[$index][3] = $reason;
		$parsed_array[$index][4] = $transaction_time;
		$parsed_array[$index][5] = $point_id;
		$parsed_array[$index][6] = $point_amount;
		$index = $index + 1;
	}

	for ($x = $start_row; $x <= $end_row; $x++)
	{
		$html_output .= '
			<tr>
				<td>'.$parsed_array[$x][1].'</td>
				<td>'.$parsed_array[$x][3].'</td>
				<td>'.$parsed_array[$x][2].'</td>
				<td>'.$parsed_array[$x][5].'</td>
				<td>'.$parsed_array[$x][6].'</td>
				<td>'.$parsed_array[$x][3].'</td>
			</tr>
				';
	}

	$html_output .= '</table>';

	return $html_output;
}
