<?php
/*
   Improved shortcode of public log.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Main Public Log shortcode function */

function vyps_public_log_func() {

	/* Technically users don't have to be logged in
	* Should litterally be the log the admin sees
	* I don't care. Tell users to not put personal identificable
	* information in their user name (referred to PID in the health care industry)
	*/

	global $wpdb;
	$table_name_points = $wpdb->prefix . 'vyps_points';
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_users = $wpdb->prefix . 'users';

	//BTW the number of IDs should always match the number of rows, NO EXCEPTIONS. If it doesn't it means the admin deleted a row
	//And that is against the psuedo-blockchain philosophy. //Also it dawned on me I can rewrite the public log here.


	//$number_of_log_rows = $wpdb->get_var( "SELECT max( id ) FROM $table_name_log" ); //No WHERE needed. All rows. No exceptions
  $number_of_log_rows_query = "SELECT max( id ) FROM ". $table_name_log;  //I'm wondering if a prepare is even needed, but throw it all in.
  $number_of_log_rows_query_prepared = $wpdb->prepare( $number_of_log_rows_query );
  $number_of_log_rows = $wpdb->get_var( $number_of_log_rows_query_prepared );

	//$number_of_point_rows = $wpdb->get_var( "SELECT max( id ) FROM $table_name_points" ); //No WHERE needed. All rows. No exceptions
  $number_of_point_rows_query = "SELECT max( id ) FROM ". $table_name_points;  //I'm wondering if a prepare is even needed, but throw it all in.
  $number_of_point_rows_query_prepared = $wpdb->prepare( $number_of_point_rows_query );
  $number_of_point_rows = $wpdb->get_var( $number_of_point_rows_query_prepared );

	//echo '<br>'. $number_of_log_rows; //Some debugging
	//echo '<br>'. $number_of_point_rows; //More debugging

	$begin_row = 1;
	$end_row = ''; //Eventually will have admin ability to filter how many rows they see as after 1000 may be intensive

	/* Although normally against totally going programatic. Since I know I'm going to reuse this for the public log I'm going to put the headers into variables */
	/* For public log the user_name should be display name and no need to see the UID and PID */
	$date_label = "Date";
	$display_name_label = "Display Name";
	$user_id_label = "UID";
	$point_type_label = "Point Type";
	$point_id_label = "PID";
	$amount_label = "Amount";
	$reason_label = "Adjustment Reason";


	//Header output is also footer output if you have not noticed.
	//Also isn't it nice you can edit the format directly instead it all in the array?
	$header_output = "
			<tr>
				<th>$date_label</th>
				<th>$display_name_label</th>
				<th>$point_type_label</th>
				<th>$amount_label</th>
				<th>$reason_label</th>
			</tr>
	";




	//Because the shorcode version won't have this
	$page_header_text = "
		<h1 class=\"wp-heading-inline\">All Point Adjustments</h1>
		<h2>Point Log</h2>
	";

	//this is what it's goint to be called
	$table_output = "";

	for ($x_for_count = $number_of_log_rows; $x_for_count > 0; $x_for_count = $x_for_count -1 ) { //I'm counting backwards. Also look what I did. Also also, there should never be a 0 id or less than 1

		//$date_data = $wpdb->get_var( "SELECT time FROM $table_name_log WHERE id= '$x_for_count'" ); //Straight up going to brute force this un-programatically not via entire row
		$date_data_query = "SELECT time FROM ". $table_name_log . " WHERE id = %d";
		$date_data_query_prepared = $wpdb->prepare( $date_data_query, $x_for_count );
		$date_data = $wpdb->get_var( $date_data_query_prepared );

		//$user_id_data = $wpdb->get_var( "SELECT user_id FROM $table_name_log WHERE id= '$x_for_count'" );
		$user_id_data_query = "SELECT user_id FROM ". $table_name_log . " WHERE id = %d";
		$user_id_data_query_prepared = $wpdb->prepare( $user_id_data_query, $x_for_count );
		$user_id_data = $wpdb->get_var( $user_id_data_query_prepared );

		//$display_name_data = $wpdb->get_var( "SELECT display_name FROM $table_name_users WHERE id= '$user_id_data'" ); //And this is why I didn't call it the entire row by arrow. We are in 4d with multiple tables
		$display_name_data_query = "SELECT display_name FROM ". $table_name_users . " WHERE id = %d"; //Note: Pulling from WP users table
		$display_name_data_query_prepared = $wpdb->prepare( $display_name_data_query, $user_id_data );
		$display_name_data = $wpdb->get_var( $display_name_data_query_prepared );

		//$point_id_data = $wpdb->get_var( "SELECT points FROM $table_name_log WHERE id= '$x_for_count'" );
		$point_id_data_query = "SELECT point_id FROM ". $table_name_log . " WHERE id = %d";
		$point_id_data_query_prepared = $wpdb->prepare( $point_id_data_query, $x_for_count );
		$point_id_data = $wpdb->get_var( $point_id_data_query_prepared );

		//$point_type_data = $wpdb->get_var( "SELECT name FROM $table_name_points WHERE id= '$point_id_data'" );
		$point_type_data_query = "SELECT point_id FROM ". $table_name_points . " WHERE id = %d";
		$point_type_data_query_prepared = $wpdb->prepare( $point_type_data_query, $point_id_data );
		$point_type_data = $wpdb->get_var( $point_type_data_query_prepared );

		//$amount_data = $wpdb->get_var( "SELECT points_amount FROM $table_name_log WHERE id= '$x_for_count'" );
    $amount_data_query = "SELECT points_amount FROM ". $table_name_log . " WHERE id = %d";
    $amount_data_query_prepared = $wpdb->prepare( $amount_data_query, $x_for_count );
    $amount_data = $wpdb->get_var( $amount_data_query_prepared );

		//$reason_data = $wpdb->get_var( "SELECT reason FROM $table_name_log WHERE id= '$x_for_count'" );
    $reason_data_query = "SELECT points_amount FROM ". $table_name_log . " WHERE id = %d";
    $reason_data_query_prepared = $wpdb->prepare( $reason_data_query, $x_for_count );
    $reason_data = $wpdb->get_var( $reason_data_query_prepared );

		//$amount_data = number_format($amount_data); //Adds commas but leaving it out here to be raw and when make [vyps-pl-tbl] will have formatting and color attributes. Also icons.

		$current_row_output = "
			<tr>
				<td>$date_data</td>
				<td>$display_name_data</td>
				<td>$point_type_data</td>
				<td>$amount_data</td>
				<td>$reason_data</td>
			</tr>
				";

		//Compile into row output.
		$table_output = $table_output . $current_row_output; //I like my way that is more reasonable instead of .=

	}

	//The page output
	echo "
		<div class=\"wrap\">
			$page_header_text
			<table class=\"wp-list-table widefat fixed striped users\">
				$header_output
				$table_output
				$header_output
			</table>
		</div>
	";

}

/*
* Shortcode for the log.
*/

add_shortcode( 'vyps-pl', 'vyps_public_log_func');
