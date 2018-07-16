<?php

//Copy of the public log file


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Added prepare() to all SQL SELECT calls 7.1.2018 */

/* Main Public Balance shortcode function */

function vyps_public_balance_func( $atts ) {

	/* Technically users don't have to be logged in
	* Should litterally be the log the admin sees
	* I don't care. Tell users to not put personal identificable
	* information in their user name (referred to PID in the health care industry)
	*/

	//Shortcode stuff
	//I'm going to eventually have site admins set logs for activities like reason etc and the meta fields, but for now.
	$atts = shortcode_atts(
		array(
				'pid' => '1',
				'reason' => '0',
				'rows' => 50,
				'bootstrap' => 'no',
				'userid' => '0',
		), $atts, 'vyps-pb' );

	$pointID = $atts['pid'];
	$reason = $atts['reason'];
	$table_row_limit = $atts['rows']; //50 by default
	$user_id = $atts['userid'];
	$boostrap_on = $atts['bootstrap'];

	global $wpdb;
	$table_name_points = $wpdb->prefix . 'vyps_points';
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

	$amount_of_pages = ceil( $number_of_users_rows / $table_row_limit); //So we know how many rows and we divide it by whatever it is and round up if not even as means maybe like one extra item over?

	//$number_of_point_rows = $wpdb->get_var( "SELECT max( id ) FROM $table_name_points" ); //No WHERE needed. All rows. No exceptions
  $number_of_point_rows_query = "SELECT max( id ) FROM ". $table_name_points;  //I'm wondering if a prepare is even needed, but throw it all in.
  $number_of_point_rows = $wpdb->get_var( $number_of_point_rows_query ); //Same issue as line 33. No real user input involved. Just server variables.

	//This will be set by the rows atts above eventually
	$begin_row = 1;
	$end_row = ''; //Eventually will have admin ability to filter how many rows they see as after 1000 may be intensive

	/* Although normally against totally going programatic. Since I know I'm going to reuse this for the public log I'm going to put the headers into variables */
	/* For public log the user_name should be display name and no need to see the UID and PID */
	$transaction_id = "Transaction ID";
	$rank_label = "Rank";
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

	//Header output is also footer output if you have not noticed.
	//Also isn't it nice you can edit the format directly instead it all in the array?
	$header_output = "
			<tr>
				<th>$rank_label</th>
				<th>$display_name_label</th>
				<th>$amount_label</th>
			</tr>
	";

	$page_button_output = ''; //Needs a define

	if ($boostrap_on == 'yes' OR $boostrap_on == 'YES' OR $boostrap_on =='Yes'){
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
	$table_range_start = $number_of_users_rows -( $table_row_limit * ( $page_number - 1 )); //Hrm... This doesn't seem like it will work.
	$table_range_stop = $number_of_users_rows - ($table_row_limit * $page_number); //I'm thinking oddly here but this should be higher.

	//Ok a catch stop for pages with more than 0 items
	if ( $table_range_stop < 1 ){

				$table_range_stop = 1; //If we go below 1, then just hard floor it at 1 as no 0 or negative transaction numbers exists.
	}

	$prior_amount = 0; //I'm throwing this in before the for as had to be initialized somewhere and if you need to mess with it, it's close by.

	//Ok. We need to just do an $x_for_count for just all the users. I really doubt you will have more than 1000 users. But we will burn that bridge when we get to it.
	for ($x_for_count = $table_range_start; $x_for_count >= $table_range_stop; $x_for_count = $x_for_count - 1 ) { //I'm counting backwards. Also look what I did. Also also, there should never be a 0 id or less than 1

		//$date_data = $wpdb->get_var( "SELECT time FROM $table_name_log WHERE id= '$x_for_count'" ); //Straight up going to brute force this un-programatically not via entire row
		/*
		* I don't think this is needed.
		$date_data_query = "SELECT time FROM ". $table_name_log . " WHERE id = %d";
		$date_data_query_prepared = $wpdb->prepare( $date_data_query, $x_for_count );
		$date_data = $wpdb->get_var( $date_data_query_prepared );
		*/

		/* We already know the user Id via x_for_count method.
		//$user_id_data = $wpdb->get_var( "SELECT user_id FROM $table_name_log WHERE id= '$x_for_count'" );
		$user_id_data_query = "SELECT user_id FROM ". $table_name_log . " WHERE id = %d";
		$user_id_data_query_prepared = $wpdb->prepare( $user_id_data_query, $x_for_count );
		$user_id_data = $wpdb->get_var( $user_id_data_query_prepared );
		*/

		//This is needed as we need the user name
		//$display_name_data = $wpdb->get_var( "SELECT display_name FROM $table_name_users WHERE id= '$user_id_data'" ); //And this is why I didn't call it the entire row by arrow. We are in 4d with multiple tables
		$display_name_data_query = "SELECT display_name FROM ". $table_name_users . " WHERE id = %d"; //Note: Pulling from WP users table
		$display_name_data_query_prepared = $wpdb->prepare( $display_name_data_query, $x_for_count );
		$display_name_data = $wpdb->get_var( $display_name_data_query_prepared );


		//Actually I think we should know this by the pid. Since the
		/*
		//$point_id_data = $wpdb->get_var( "SELECT point_id FROM $table_name_log WHERE id= '$x_for_count'" );
		$point_id_data_query = "SELECT point_id FROM ". $table_name_log . " WHERE id = %d";
		$point_id_data_query_prepared = $wpdb->prepare( $point_id_data_query, $x_for_count );
		$point_id_data = $wpdb->get_var( $point_id_data_query_prepared );
		*/

		//And I don't think we need this either.
		/*
		//$point_type_data = $wpdb->get_var( "SELECT name FROM $table_name_points WHERE id= '$point_id_data'" );
		$point_type_data_query = "SELECT name FROM ". $table_name_points . " WHERE id = %d";
		$point_type_data_query_prepared = $wpdb->prepare( $point_type_data_query, $point_id_data );
		$point_type_data = $wpdb->get_var( $point_type_data_query_prepared );
		*/

		//We do need this.
		//$amount_data = $wpdb->get_var( "SELECT points_amount FROM $table_name_log WHERE id= '$x_for_count'" );
    $amount_data_query = "SELECT sum(points_amount) FROM ". $table_name_log . " WHERE user_id = %d AND point_id = %d";
    $amount_data_query_prepared = $wpdb->prepare( $amount_data_query, $x_for_count, $pointID ); //pulling this from the shortcode atts, by default its 1. Technically it won't work without a coin, but *shrugs*
    $amount_data = $wpdb->get_var( $amount_data_query_prepared );

		$amount_data = intval($amount_data); //need to set this a int and if it's zero then ignore the output. BTW I should put less than, but I think negative numbers and zeroes have their place

		//Ok going to check to see if the display named returned anything and if now, then blank it out.
		if ( $display_name_data == '' ){

			$current_row_output = ''; //No output if there is no name. Probaly not an amount either.

		} else {

			$current_amount = $amount_data; //Saving this for comparison. As we know this row is valid we only need to change variable now.

			$current_row_output = "
				<tr>
					<td>$x_for_count</td>
					<td>$display_name_data</td>
					<td>$amount_data</td>
				</tr>
					";
		}

		//Don't need the reason as far as I know... Might be useful down the road
		/*
		//$reason_data = $wpdb->get_var( "SELECT reason FROM $table_name_log WHERE id= '$x_for_count'" );
    $reason_data_query = "SELECT reason FROM ". $table_name_log . " WHERE id = %d";
    $reason_data_query_prepared = $wpdb->prepare( $reason_data_query, $x_for_count );
    $reason_data = $wpdb->get_var( $reason_data_query_prepared );
		*/

		//$amount_data = number_format($amount_data); //Adds commas but leaving it out here to be raw and when make [vyps-pb-tbl] will have formatting and color attributes. Also icons.

		//Compile into row output.

		//Some weird logic I have had an idea for... To sort the table. One doesn't have to deal with sql at all. They just need to check the table to see if the user before or
		//After it to determine if $table_output = $table_output . $current_row_output; vs $table_output = $current_row_output . $table_output;  Seems stupidly obvious
		//Now that I think about its o don't have to deal with arrays or loops within loops. Cells in leaderboards interlinked.

		if ( $display_name_data == '' ){

			//Nothing really need to happen here (I suppose I could do =!). I have this here as I have a feeling I might need it later rather than just a =!

		} else {

			//I can't tell but I think this needs to only run when there is need to rather than all the time.
			if ( $current_amount > $prior_amount ) {

				$table_output = $current_row_output . $table_output; //If the current was bigger in the amount, put that current goes first

				//I'm not 100% sure of this placement. But this might work.
				$prior_amount = $current_amount; //Ok, we need to make prior only when there is an actual display name. Otherwise it doesn't chagne here either in case of back to back blanks

			} elseif ( $current_amount <= $prior_amount ) {

				$table_output = $table_output . $current_row_output; //If the prior was bigging current goes after. Or equal, it doesn't matter. Also no need for two elseifs that way

			}

		//End of checking for blank display name.
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

/*
* Shortcode for the log.
*/

add_shortcode( 'vyps-pb', 'vyps_public_balance_func');
