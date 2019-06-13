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

	//SQL query of current user on log
	$user_data_query = "SELECT * FROM ". $table_name_log . " WHERE user_id = %d";
	$user_data_query_prepared = $wpdb->prepare( $user_data_query, $user_id );
	$user_data = $wpdb->get_results( $user_data_query_prepared );

	//SQL query of user display name
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

	$index = 0;

	//this code below checks the gets and determines the page nation
	if (isset($_GET['action']))
	{
		$page_number = intval(htmlspecialchars($_GET['action']));
	}
	else
	{
		$page_number = 1; //Well... Always first.
	}

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
		$parsed_array[$index]['index'] = $index;
		$parsed_array[$index]['transaction_id'] = $transaction_id;
		$parsed_array[$index]['user_id'] = $user_id;
		$parsed_array[$index]['reason'] = $reason;
		$parsed_array[$index]['transaction_time'] = $transaction_time;
		$parsed_array[$index]['point_id'] = $point_id;
		$parsed_array[$index]['point_amount'] = $point_amount;
		$index = $index + 1;
	}

	//This had to go below the foreach and use the $index for number of rows
	$amount_of_pages = ceil( $index / $table_row_limit);
	$display_name = vidyen_user_display_name($user_id);

	//Below is the HTML output for the pagenation
	$html_output = '<h1>'.$display_name.'\'s Transaction Log</h1>
		<ul class="pagination">
		<li><a href="?action=1">Oldest</a></li>'; //First boot strap

	if ( $amount_of_pages < $max_pages_middle)
	{
		 $page_number_start = 1;
		 $page_number_end  = $amount_of_pages;
	}
	elseif ($page_number > $max_pages_middle AND $page_number <= ($amount_of_pages - $max_pages_middle )) //logic time here. If page number selected is greater than 5, it means we start removing the 1 to only show 9. I'll fix the math later
	{
		$page_number_start = $page_number - $max_pages_middle;
		$page_number_end = $page_number + $max_pages_middle;
	}
	elseif( $page_number >= ($amount_of_pages - $max_pages_middle ))
	{
		$page_number_start = $amount_of_pages - $max_pages_middle;
		$page_number_end = $amount_of_pages;
	}
	else
	{
		$page_number_start = 1;
		$page_number_end = $max_pages;
	}

	//Ok. Just going to loop for nubmer of pages.
	for ($p_for_count = $page_number_start; $p_for_count <= $page_number_end; $p_for_count = $p_for_count + 1 )
	{
		$page_button = "<li><a href=\"?action=$p_for_count\">$p_for_count</a></li>";

		$html_output .= $page_button;
		//end for
	}

	$html_output .= '<li><a href="?action='.$amount_of_pages.'">Newest</a></li></ul>';

	//$html_output = 'Begin<br><br>';
	$html_output .= '<table width="100%">';
	$html_output .= "
			<tr>
				<th>$transaction_id_label</th>
				<th>$date_label</th>
				<th>$point_type_label</th>
				<th>$amount_label</th>
				<th>$reason_label</th>
			</tr>
	";

	//Have to do a trick to find start and end row
	$start_row =  ($page_number * $table_row_limit ) - $table_row_limit;
	$end_row = ($page_number * $table_row_limit );

	//Sometimes the rows are less than the max on the last page.
	if ($end_row > $index)
	{
			$end_row = $index - 1; //Should be the last row at the end minus the last lop. Goddamn loop logic.
	}

	for ($x = $start_row; $x <= $end_row; $x++)
	{
		$html_output .= '
			<tr>
				<td>'.$parsed_array[$x]['transaction_id'].'</td>
				<td>'.$parsed_array[$x]['transaction_time'].'</td>
				<td>'.$parsed_array[$x]['point_id'].'</td>
				<td>'.$parsed_array[$x]['point_amount'].'</td>
				<td>'.$parsed_array[$x]['reason'].'</td>
			</tr>
				';
	}

	$html_output .= '</table>';

	return $html_output;
}
