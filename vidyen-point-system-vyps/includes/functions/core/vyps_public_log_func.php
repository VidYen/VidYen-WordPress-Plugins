<?php

//Improved shortcode of public log.


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Added prepare() to all SQL SELECT calls 7.1.2018 */

/* Main Public Log shortcode function */

function vyps_public_log_func( $atts ) {

	/* Technically users don't have to be logged in
	* Should litterally be the log the admin sees
	* I don't care. Tell users to not put personal identificable
	* information in their user name (referred to PID in the health care industry)
	*/

	//Shortcode stuff
	//I'm going to eventually have site admins set logs for activities like reason etc and the meta fields, but for now.
	$atts = shortcode_atts(
		array(
				'pid' => '0',
				'reason' => '0',
				'rows' => 50,
				'bootstrap' => 'no',
				'userid' => 0,
				'uid' => FALSE,
				'admin' => FALSE,
				'current' => FALSE,
		), $atts, 'vyps-pl' );

	$pointID = $atts['pid'];
	$reason = $atts['reason'];
	$table_row_limit = $atts['rows']; //50 by default
	$user_id = $atts['userid'];
	$uid_on = $atts['uid'];
	$boostrap_on = $atts['bootstrap'];
	$admin_on = $atts['admin'];
	$current_user_state = $atts['current'];

	//So users can see their own transcations, I'm putting this shortcode hoook in.
	if ($current_user_state == TRUE)
	{
		$user_id = get_current_user_id(); //Over riding the current userid to show just the current user. I have no idea if this actually works as may have not set it up correctly.
	}

	global $wpdb;
	$table_name_points = $wpdb->prefix . 'vyps_points';
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_users = $wpdb->prefix . 'users';

	//BTW the number of IDs should always match the number of rows, NO EXCEPTIONS. If it doesn't it means the admin deleted a row
	//And that is against the psuedo-blockchain philosophy. //Also it dawned on me I can rewrite the public log here.


	//$number_of_log_rows = $wpdb->get_var( "SELECT max( id ) FROM $table_name_log" ); //No WHERE needed. All rows. No exceptions
  $number_of_log_rows_query = "SELECT max( id ) FROM ". $table_name_log;  //I'm wondering if a prepare is even needed, but throw it all in.
  $number_of_log_rows = $wpdb->get_var( $number_of_log_rows_query ); //Ok. I realized that not only prepare() doesn't work it, there is no varialbes needed to sanitize as the table name is actually hard coded.

	$amount_of_pages = ceil($number_of_log_rows / $table_row_limit); //So we know how many rows and we divide it by whatever it is and round up if not even as means maybe like one extra item over?

	//$number_of_point_rows = $wpdb->get_var( "SELECT max( id ) FROM $table_name_points" ); //No WHERE needed. All rows. No exceptions
  $number_of_point_rows_query = "SELECT max( id ) FROM ". $table_name_points;  //I'm wondering if a prepare is even needed, but throw it all in.
  $number_of_point_rows = $wpdb->get_var( $number_of_point_rows_query ); //Same issue as line 33. No real user input involved. Just server variables.

	//This will be set by the rows atts above eventually
	$begin_row = 1;
	$end_row = ''; //Eventually will have admin ability to filter how many rows they see as after 1000 may be intensive

	/* Although normally against totally going programatic. Since I know I'm going to reuse this for the public log I'm going to put the headers into variables */
	/* For public log the user_name should be display name and no need to see the UID and PID */
	$transaction_id = "Transaction ID";
	$date_label = "Date";
	$display_name_label = "Display Name";
	$user_id_label = "UID";
	$point_type_label = "Point Type";
	$point_id_label = "PID";
	$amount_label = "Amount";
	$reason_label = "Adjustment Reason";

	//this code below checks the gets and determines the page nation
	if (isset($_GET['action'])){

		$page_number = intval(htmlspecialchars($_GET['action']));

	} else{

		$page_number = 1; //Well... Always first.

	}

	//Adding the UID option to show in the admin panel of if the admin wants to turn on the public log for some reason.
	//NOTE: have decided to use the function log as the log itself.
	if ( $uid_on == TRUE ){

		$uid_label_row = "<th>$user_id_label</th>";

	} else {

		$uid_label_row = ""; //Defined and set to blank to if need to display.

	}

	//Header output is also footer output if you have not noticed.
	//Also isn't it nice you can edit the format directly instead it all in the array?
	$header_output = "
			<tr>
				<th>$transaction_id</th>
				<th>$date_label</th>
				$uid_label_row
				<th>$display_name_label</th>
				<th>$point_type_label</th>
				<th>$amount_label</th>
				<th>$reason_label</th>
			</tr>
	";

	$page_button_output = ''; //Needs a define

	if ( $admin_on == TRUE AND $user_id == 0 ) {

		//This is for the admin menus. Have to have special to add the link for page=vyps_admin_log and its just the whole log AKA user_id == 0
		//NOTE: No need for nonce as not inputing data or POST-ing //NOTE NOTE I feel this is lazy coding and should be revisted later.
		for ($p_for_count = 1; $p_for_count <= $amount_of_pages; $p_for_count = $p_for_count + 1 ) {

			$page_button = "<a href=\"?page=admin_log&action=$p_for_count\">$p_for_count</a>&nbsp;|&nbsp;";

			$page_button_output = $page_button_output . $page_button;
			//end for
		}

		$page_button_row_output = "
			<div class=\"pagination\">
				$page_button_output
			</div>";
		//end of non bootstrap else

	} elseif ( $admin_on == TRUE AND $user_id > 0 ) {

		//NOTE: Due to the issues with nonce, I'm just goin got show the last transactions.

		$page_button_output = "User Point Log - $table_row_limit most recent";
		$page_button_row_output = "<ul class=\"pagination\">$page_button_output</ul>";

		//I'm going to need to figure out what to do about the page headers here.

	} elseif ($boostrap_on == 'yes' OR $boostrap_on == 'YES' OR $boostrap_on =='Yes'){

		//Ok. Just going to loop for nubmer of pages.
		for ($p_for_count = 1; $p_for_count <= $amount_of_pages; $p_for_count = $p_for_count + 1 ) {

			$page_button = "<li><a href=\"?action=$p_for_count\">$p_for_count</a></li>";

			$page_button_output = $page_button_output . $page_button;
			//end for
		}

		$page_button_row_output = "
			<ul class=\"pagination\">
				$page_button_output
			</ul>";
		//end of bootstrap if

	} else {

		//this meeans we got no boostrap so it's just links.
		//Ok. Just going to loop for nubmer of pages.
		for ($p_for_count = 1; $p_for_count <= $amount_of_pages; $p_for_count = $p_for_count + 1 ) {

			$page_button = "<a href=\"?action=$p_for_count\">$p_for_count</a>&nbsp;|&nbsp;";

			$page_button_output = $page_button_output . $page_button;
			//end for
		}

		$page_button_row_output = "
			<div class=\"pagination\">
				$page_button_output
			</div>";
		//end of non bootstrap else

	}


	//Because the shortcode version won't have this
	//	<h1 class=\"wp-heading-inline\">Public Point Log</h1> this was commented out. I don't think it was needed as admin can put any text in they want.
	$page_header_text = "
			$page_button_row_output
			";

	//this is what it's goint to be called
	$table_output = "";

	//Ok I got logic here that I think will work. the > will always be $table_range_stop = $number_of_log_rows - ($number_of_log_rows - $table_row_limit ) or $current_rows_output number.
	//OLD: for ($x_for_count = $number_of_log_rows; $x_for_count > 0; $x_for_count = $x_for_count -1 ) {
	$table_range_start = $number_of_log_rows -( $table_row_limit * ( $page_number - 1 )); //Hrm... This doesn't seem like it will work.
	$table_range_stop = $number_of_log_rows - ($table_row_limit * $page_number); //I'm thinking oddly here but this should be higher.

	//Ok a catch stop for pages with more than 0 items
	if ( $table_range_stop < 1 ){

				$table_range_stop = 1; //If we go below 1, then just hard floor it at 1 as no 0 or negative transaction numbers exists.
	}

	//The number of log rows will always but correct but its the starting point and end points that will change.
	for ($x_for_count = $table_range_start; $x_for_count >= $table_range_stop; $x_for_count = $x_for_count - 1 ) { //I'm counting backwards. Also look what I did. Also also, there should never be a 0 id or less than 1

		//$date_data = $wpdb->get_var( "SELECT time FROM $table_name_log WHERE id= '$x_for_count'" ); //Straight up going to brute force this un-programatically not via entire row
		$date_data_query = "SELECT time FROM ". $table_name_log . " WHERE id = %d";
		$date_data_query_prepared = $wpdb->prepare( $date_data_query, $x_for_count );
		$date_data = $wpdb->get_var( $date_data_query_prepared );

		//$user_id_data = $wpdb->get_var( "SELECT user_id FROM $table_name_log WHERE id= '$x_for_count'" );
		$user_id_data_query = "SELECT user_id FROM ". $table_name_log . " WHERE id = %d";
		$user_id_data_query_prepared = $wpdb->prepare( $user_id_data_query, $x_for_count );
		$user_id_data = $wpdb->get_var( $user_id_data_query_prepared );
		$user_id_validated = intval($user_id_data); //I added this extra line to make the return an int as it wasn't being compared correctly as was coming out as a string not a number.

		//$display_name_data = $wpdb->get_var( "SELECT display_name FROM $table_name_users WHERE id= '$user_id_data'" ); //And this is why I didn't call it the entire row by arrow. We are in 4d with multiple tables
		$display_name_data_query = "SELECT display_name FROM ". $table_name_users . " WHERE id = %d"; //Note: Pulling from WP users table
		$display_name_data_query_prepared = $wpdb->prepare( $display_name_data_query, $user_id_data );
		$display_name_data = $wpdb->get_var( $display_name_data_query_prepared );

		//$point_id_data = $wpdb->get_var( "SELECT point_id FROM $table_name_log WHERE id= '$x_for_count'" );
		$point_id_data_query = "SELECT point_id FROM ". $table_name_log . " WHERE id = %d";
		$point_id_data_query_prepared = $wpdb->prepare( $point_id_data_query, $x_for_count );
		$point_id_data = $wpdb->get_var( $point_id_data_query_prepared );

		//$point_type_data = $wpdb->get_var( "SELECT name FROM $table_name_points WHERE id= '$point_id_data'" );
		$point_type_data_query = "SELECT name FROM ". $table_name_points . " WHERE id = %d";
		$point_type_data_query_prepared = $wpdb->prepare( $point_type_data_query, $point_id_data );
		$point_type_data = $wpdb->get_var( $point_type_data_query_prepared );

		//$amount_data = $wpdb->get_var( "SELECT points_amount FROM $table_name_log WHERE id= '$x_for_count'" );
    $amount_data_query = "SELECT points_amount FROM ". $table_name_log . " WHERE id = %d";
    $amount_data_query_prepared = $wpdb->prepare( $amount_data_query, $x_for_count );
    $amount_data = $wpdb->get_var( $amount_data_query_prepared );

		//$reason_data = $wpdb->get_var( "SELECT reason FROM $table_name_log WHERE id= '$x_for_count'" );
    $reason_data_query = "SELECT reason FROM ". $table_name_log . " WHERE id = %d";
    $reason_data_query_prepared = $wpdb->prepare( $reason_data_query, $x_for_count );
    $reason_data = $wpdb->get_var( $reason_data_query_prepared );

		//If statement to pop in the UID if There
		//Just popping in to the table if there. Hopefully it doesn't blow up the existing table
		if( $uid_on == TRUE ){

			$uid_data_row = "<td>$user_id_data</td>";

		}	else {

			$uid_data_row = "";

		}

		$current_row_output = "
			<tr>
				<td>$x_for_count</td>
				<td>$date_data</td>
				$uid_data_row
				<td>$display_name_data</td>
				<td>$point_type_data</td>
				<td>$amount_data</td>
				<td>$reason_data</td>
			</tr>
				";

		//Code inserted to see if a user id was specified. If so, we are creating a table just for that user_id.
		if( $user_id == 0){

			//Compile into row output.
			$table_output = $table_output . $current_row_output; //I like my way that is more reasonable instead of .=

		} elseif ( $user_id_validated == $user_id AND $user_id > 0) {

			//The idea above is to see if the query for the sql UID pull (validated) $user_id is the same as the query AND is greater than zero.
			//In theory, you could put a negative number in, but not sure why, but never trust your users not to try.
			//I believe there should be either it is 0 or above zero and equals but never anything else so we should be good.
			$table_output = $table_output . $current_row_output; //I like my way that is more reasonable instead of .=

		}

	}

	//The page output
	return "
		<div class=\"wrap\">
			<h2 style=\"text-align:center\">Page $page_number</h2>
			$page_header_text
			<table class=\"wp-list-table widefat fixed striped users\">
				$header_output
				$table_output
				$header_output
			</table>
			$page_button_row_output
			<h2 style=\"text-align:center\">Page $page_number</h2>
		</div>
	";

}
