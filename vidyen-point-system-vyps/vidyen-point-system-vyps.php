<?php

 /*
Plugin Name:  VidYen Point System [VYPS]
Plugin URI:   http://vyps.org
Description:  VidYen Point System [VYPS] allows you to create a rewards site using video ads or browser mining.
Version:      00.04.11.01
Author:       VidYen, LLC
Author URI:   https://vidyen.com/
License:      GPLv2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Ok. I'm adding a custom fuction to the VYPS plugin. It's put on pages where, you just want to straight kick people out if they aren't admins.
//Similar to the login check, but the admins. This will put put on all pages that only admins should be able to see but not the shortcodes results.

function VYPS_check_if_true_admin(){

	//I'm going to be a little lenient and if you can edit users maybe you should be able to edit their point since you can just
	//Change roles at that point. May reconsider.
	if( current_user_can('install_plugin') OR current_user_can('edit_users') ){

		//echo "You good!"; //Debugging
		return;

	} else {

		echo "<br><br>You need true administrator rights to see this page!"; //Debugging
		exit; //Might be a better solution to iform before exit like an echo before hand, but well....
	}

}

register_activation_hook(__FILE__, 'vyps_points_install');

//Install the SQL tables for VYPS.
function vyps_points_install() {

    global $wpdb;

		//I have no clue why this is needed. I should learn, but I wasn't the original author. -Felty
		$charset_collate = $wpdb->get_charset_collate();

		//NOTE: I have the mind to make mediumint to int, but I wonder if you get 8 million log transactios that you should consider another solution than VYPS.

		//vyps_points table creation
    $table_name_points = $wpdb->prefix . 'vyps_points';

    $sql = "CREATE TABLE {$table_name_points} (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		name tinytext NOT NULL,
		icon text NOT NULL,
		PRIMARY KEY  (id)
        ) {$charset_collate};";

		//vyps_points_log. Notice how I loath th keep variable names the same in recycled code.
		//Visualization people. It's better for code to be ineffecient but readable than efficient and unreadable.
    $table_name_points_log = $wpdb->prefix . 'vyps_points_log';

    $sql .= "CREATE TABLE {$table_name_points_log} (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
                reason varchar(128) NOT NULL,
                user_id mediumint(9) NOT NULL,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		point_id varchar(11) NOT NULL,
                points_amount double(64, 0) NOT NULL,
                adjustment varchar(100) NOT NULL,
								vyps_meta_id varchar(64) NOT NULL,
								vyps_meta_data varchar(128) NOT NULL,
								vyps_meta_amount double(64,0) NOT NULL,
								vyps_meta_subid1 mediumint(9) NOT NULL,
								vyps_meta_subid2 mediumint(9) NOT NULL,
								vyps_meta_subid3 mediumint(9) NOT NULL,
		PRIMARY KEY  (id)
        ) {$charset_collate};";

    require_once (ABSPATH . 'wp-admin/includes/upgrade.php'); //I am concerned that this used ABSPATH rather than the normie WP methods

    dbDelta($sql);
}

//adding menues
add_action('admin_menu', 'vyps_points_menu');

function vyps_points_menu() {

    $parent_page_title = "VidYen Point System";
    $parent_menu_title = 'VYPS';
    $capability = 'manage_options';
    $parent_menu_slug = 'vyps_points';
    $parent_function = 'vyps_points_parent_menu_page';
    add_menu_page($parent_page_title, $parent_menu_title, $capability, $parent_menu_slug, $parent_function);

    $page_title = "Manage Points";
    $menu_title = 'Points List';
    $menu_slug = 'vyps_points_list';
    $function = 'vyps_points_sub_menu_page';
    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

    $page_title = "Add Point";
    $menu_title = 'Add Point';
    $menu_slug = 'vyps_points_add';
    $function = 'vyps_points_add_sub_menu_page';
    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

    $page_title = "Point Log";
    $menu_title = 'Point Log';
    $menu_slug = 'admin_log';
    $function = 'vyps_admin_log';
    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}

/*** Menu Includes ***/

include( plugin_dir_path( __FILE__ ) . 'includes/menus/as_menu.php'); //Adscend menu 400 order
include( plugin_dir_path( __FILE__ ) . 'includes/menus/ch_menu.php'); //Coinhive menu 430 order
include( plugin_dir_path( __FILE__ ) . 'includes/menus/vy256_menu.php'); //Coinhive menu 440 order

/*** End of Menu Includes ***/

//Below is the admin log function that I intend to move to includes eventually.
function vyps_admin_log() {

  global $wpdb;
	$table_name_points = $wpdb->prefix . 'vyps_points';
	$table_name_log = $wpdb->prefix . 'vyps_points_log';
	$table_name_users = $wpdb->prefix . 'users';

	//BTW the number of IDs should always match the number of rows, NO EXCEPTIONS. If it doesn't it means the admin deleted a row
	//And that is against the psuedo-blockchain philosophy. //Also it dawned on me I can rewrite the public log here.

	$number_of_log_rows = $wpdb->get_var( "SELECT max( id ) FROM $table_name_log" ); //No where needed. All rows. No exceptions
	$number_of_point_rows = $wpdb->get_var( "SELECT max( id ) FROM $table_name_points" ); //No where needed. All rows. No exceptions

	//echo '<br>'. $number_of_log_rows; //Some debugging
	//echo '<br>'. $number_of_point_rows; //More debugging

	$begin_row = 1;
	$end_row = ''; //Eventually will have admin ability to filter how many rows they see as after 1000 may be intensive

	/* Although normally against totally going programatic. Since I know I'm going to reuse this for the public log I'm going to put the headers into variables */

	$date_label = "Date";
	$user_name_label = "User Name";
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
				<th>$user_name_label</th>
				<th>$user_id_label</th>
				<th>$point_type_label</th>
				<th>$point_id_label</th>
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

    //$user_name_data = $wpdb->get_var( "SELECT user_login FROM $table_name_users WHERE id= '$user_id_data'" ); //And this is why I didn't call it the entire row by arrow. We are in 4d with multiple tables
    $user_name_data_query = "SELECT user_login FROM ". $table_name_users . " WHERE id = %d"; //Note: Pulling from WP users table
    $user_name_data_query_prepared = $wpdb->prepare( $user_name_data_query, $user_id_data );
    $user_name_data = $wpdb->get_var( $user_name_data_query_prepared );

    //$point_id_data = $wpdb->get_var( "SELECT points FROM $table_name_log WHERE id= '$x_for_count'" );
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


		$current_row_output = "
			<tr>
				<td>$date_data</td>
				<td>$user_name_data</td>
				<td>$user_id_data</td>
				<td>$point_type_data</td>
				<td>$point_id_data</td>
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

/* Main page informational page. Includes shortcodes, advertistments etc */

function vyps_points_parent_menu_page() {

	//Logo from base. If a plugin is installed not on the menu they can't see it not showing.
	echo '<br><br><img src="' . plugins_url( '../vidyen-point-system-vyps/images/logo.png', __FILE__ ) . '" > ';

	//Static text for the base plugin
	echo
	"<h1>VidYen Point System Base Plugin</h1>
	<p>VYPS allows you to gamify monetization by giving your users a reason to turn off adblockers in return for rewards and recognition.</p>
	<p>This is a multipart system - similar to WooCommerce - which allows WordPress administrators to track points for rewards in monetization systems.</p>
	<p>To prevent catastrophic data loss, uninstalling this plugin will no longer automatically delete the VYPS user data. To drop your VYPS tables from the WPDB, use the VYPS Uninstall plugin to do a clean install.</p>
	<br>
	<h2>Base Plugin Instructions</h2>
	<p>Add points by navigating to the Add Points menu.</p>
	<p>To modify or see a user’s current point balance, go to the Users panel and use the context menu by &quot;Edit User Information&quot; under &quot;Edit Points&quot;.</p>
	<p>To see a log of all user transactions, go to &quot;Point Log&quot; in the VidYen Points menu.</p>
	";

	/* This is the credits.php which only needs to be modified in the base to show on all addon plugins
	*  Credit for this fix goes to skotperez off stack exchange for his answer on Nov 2, 2016
	*  https://stackoverflow.com/questions/32177667/include-a-php-file-in-another-php-file-wordpress-plugin
	*  I added the ../ to make it work in my case though.
	*/

	include( plugin_dir_path( __FILE__ ) . '../vidyen-point-system-vyps/includes/sc_instruct.php');
	include( plugin_dir_path( __FILE__ ) . '../vidyen-point-system-vyps/includes/credits.php');

}

function vyps_points_sub_menu_page() {
    global $wpdb;
    require plugin_dir_path(__FILE__) . 'manage_points.php';
}

function vyps_points_add_sub_menu_page() {
    global $wpdb;
    require plugin_dir_path(__FILE__) . 'add_point.php';
}

add_action('show_user_profile', 'custom_user_profile_fields_points');
add_action('edit_user_profile', 'custom_user_profile_fields_points');
add_action("user_new_form", "custom_user_profile_fields_points");

//start add new column points in user table
//BTW I prefixed the next two functions with vyps_ as I have a feeling that might be used by other plugins
//Since it was generic

function vyps_register_custom_user_column($columns) {
    $columns['points'] = 'Points';
    return $columns;
}

/* The next function is important to show the points in the user table */

function vyps_register_custom_user_column_view($value, $column_name, $user_id) {
    $user_info = get_userdata($user_id);
    global $wpdb;
    $query_row = "select *, sum(points_amount) as sum from {$wpdb->prefix}vyps_points_log group by point_id, user_id having user_id = '{$user_id}'";
    $row_data = $wpdb->get_results($query_row);

		//I need to update this eventually. I realized I didn't fix this, but its only calling non-user input data from the WPDB. I still don't like the -> in fact I hate -> calls
    $points = '';
    if (!empty($row_data)) {
        foreach($row_data as $type){
            $query_for_name = "select * from {$wpdb->prefix}vyps_points where id= '{$type->point_id}'";
            $row_data2 = $wpdb->get_row($query_for_name);
            $points .= '<b>' . $type->sum . '</b> ' . $row_data2->name. '<br>';
        }
    } else {
        $points = '';
    }

    if ($column_name == 'points')
        return $points;
    return $value;
}

add_action('manage_users_columns', 'vyps_register_custom_user_column');
add_action('manage_users_custom_column', 'vyps_register_custom_user_column_view', 10, 3);

//BTW this was all original from orion (Are they ever getting the daily login). I have no clue what cgc_ub_action_links stands for but I know what it does. I'll call it something more informative.
function vyps_user_menu_action_links($actions, $user_object) {

		//Ok. The nonce.
		$vyps_nonce_check = wp_create_nonce( 'vyps-nonce' );
    $actions['edit_points'] = "<a class='cgc_ub_edit_badges' href='" . admin_url("admin.php?page=vyps_points_list&edituserpoints=$user_object->ID&_wpnonce=$vyps_nonce_check") . "'>" . __('Edit Points') . "</a>";
    return $actions;
}

add_filter('user_row_actions', 'vyps_user_menu_action_links', 10, 2);

/*** SHORTCODE INCLUDES IN BASE ***/

//It has dawned on me that the ../vidyen-point-etc may not be needed actually?

//include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/debug.php'); //We got so complicated needed to help users troubleshoot server errors. Left off when not needed but will make more detailed later.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypspl.php'); //Point Log
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypsbc.php'); //Balance shortcode
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypsbc_ww.php'); //Balance for woowallet as the built in one annoys me with refresh update
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypspt.php'); //Point Transfer shorcode raw format. Maybe should rename to vypspt_raw.php
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypspt_tbl.php'); //Point Transfer Table code. One day. I'm goign to retire PT, but admins might need it.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypspt_ww.php'); //WW point transfer bridge Shortcode table
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypslg.php'); //You are not logged in blank shortcode.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypsch.php'); //Rolling the Coinhive in.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypsas.php'); //Rolling the Adscend in. I hate ads but I'm being pragmatic
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypstr.php'); //Threshold Raffle shortcode. This is going to be cool
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypstr_cl.php'); //Current game log so you can see progress. Need to work on a game history log.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypspb.php'); //Point balances for public viewing (and maybe some leaderboard stuff)
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps256.php'); //VYPS webminerpool shortcode
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps256_dev.php'); //Developement version
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps256_debug.php'); //Debug version that shows output. Does not throttle btw
/*** End of Shortcode Includes ***/
