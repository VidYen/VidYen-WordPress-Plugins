<?php
/*
  Plugin Name: VidYen Point System
  Description: VidYen Point System [VYPS] allows you to gamify monetization by giving your users a reason to turn off adblockers for rewards.
  Version: 0.0.48
  Author: VidYen, LLC
  Author URI: https://vidyen.com/
  License: GPLv2 or later
 */

register_activation_hook(__FILE__, 'vyps_points_install');

function vyps_points_install() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'vyps_points';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table_name} (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		name tinytext NOT NULL,
		icon text NOT NULL,
		points varchar(11) NOT NULL,
		PRIMARY KEY  (id)
        ) {$charset_collate};";

    $table_name = $wpdb->prefix . 'vyps_points_log';

	/* As number_format() seems to solve most of the display problems I have added .16 decimals
	*  so it doesn't screw up everything. That said. I will need to test on fresh copy and so on
	*  to make sure it installs without blowing up
	*/
	
    $sql .= "CREATE TABLE {$table_name} (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
                reason tinytext NOT NULL,
                user_id mediumint(9) NOT NULL,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		points varchar(11) NOT NULL,
                points_amount double(64, 0) NOT NULL,
                adjustment varchar(100) NOT NULL,
		PRIMARY KEY  (id)
        ) {$charset_collate};";

    require_once (ABSPATH . 'wp-admin/includes/upgrade.php'); //I am concerned that this used ABSPATH rather than the normie WP methods

    dbDelta($sql);
}
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
	
		$date_data = $wpdb->get_var( "SELECT time FROM $table_name_log WHERE id= '$x_for_count'" ); //Straight up going to brute force this un-programatically not via entire row
		$user_id_data = $wpdb->get_var( "SELECT user_id FROM $table_name_log WHERE id= '$x_for_count'" );
		$user_name_data = $wpdb->get_var( "SELECT user_login FROM $table_name_users WHERE id= '$user_id_data'" ); //And this is why I didn't call it the entire row by arrow. We are in 4d with multiple tables
		$point_id_data = $wpdb->get_var( "SELECT points FROM $table_name_log WHERE id= '$x_for_count'" ); //Yeah this is why I want to call points something else in this table, but its the PID if you can't tell
		$point_type_data = $wpdb->get_var( "SELECT name FROM $table_name_points WHERE id= '$point_id_data'" ); //And now we are calling a total of 3 tables in this operation
		$amount_data = $wpdb->get_var( "SELECT points_amount FROM $table_name_log WHERE id= '$x_for_count'" );
		$reason_data = $wpdb->get_var( "SELECT reason FROM $table_name_log WHERE id= '$x_for_count'" );
		
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
	echo '<br><br><img src="' . plugins_url( '../VYPS/images/logo.png', __FILE__ ) . '" > ';

	//Static text for the base plugin
	echo
	"<h1>VidYen Point System Base Plugin</h1>
	<p>VYPS allows you to gamify monetization by giving your users a reason to turn off adblockers for rewards.</p>
	<p>This is a multipart system similar to WooCommerce as it intends to allow WordPress administrators to create points for monetization and rewards into other system.</p>
	<p>To prevent catastrophic data loss, uninstalling this plugin will no longer automatically delete the VYPS user data. To clean you WPDB, use the VYPS Uninstall plugin if you really need to do a clean install.</p>
	<br>
	<h2>Base Plugin Instructions</h2>
	<p>Add points put navigating to the Add Point list.</p>
	<p>To modify or see a users current point balance go to the users panel and use the context menu by edit information under &quot;Edit Points&quot;.</p>
	<p>To see a log of all user transactions, go to &quot;All Point Adjustments&quot; in the VidYen Points menu.</p>
	
	";
	
	/* This is the credits.php which only needs to be modified in the base to show on all addon plugins
	*  Credit for this fix goes to skotperez off stack exchange for his answer on Nov 2, 2016
	*  https://stackoverflow.com/questions/32177667/include-a-php-file-in-another-php-file-wordpress-plugin
	*  I added the ../ to make it work in my case though.
	*/
	
	include( plugin_dir_path( __FILE__ ) . '../VYPS/includes/sc_instruct.php'); 
	include( plugin_dir_path( __FILE__ ) . '../VYPS/includes/credits.php'); 
	
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

function register_custom_user_column($columns) {
    $columns['points'] = 'Points';
    return $columns;
}

/* The next function is important to show the points in the user table */

function register_custom_user_column_view($value, $column_name, $user_id) {
    $user_info = get_userdata($user_id);
    global $wpdb;
    $query_row = "select *, sum(points_amount) as sum from {$wpdb->prefix}vyps_points_log group by points, user_id having user_id = '{$user_id}'";
    $row_data = $wpdb->get_results($query_row);

//    echo "<pre>";
//    print_r($row_data);
//    die;
    
    $points = '';
    if (!empty($row_data)) {
        foreach($row_data as $type){
            $query_for_name = "select * from {$wpdb->prefix}vyps_points where id= '{$type->points}'";
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

add_action('manage_users_columns', 'register_custom_user_column');
add_action('manage_users_custom_column', 'register_custom_user_column_view', 10, 3);

//end of add column in user table

if (isset($_POST['updateusers'])) {


    global $wpdb;
    $table = $wpdb->prefix . 'vyps_points_log';
    $data = [
        'points' => $_POST['points'],
        'user_id' => $_POST['updateusers'],
        'time' => date('Y-m-d H:i:s')
    ];


    $wpdb->update($table, $data, ['user_id' => $_POST['updateusers']]);

    $message = "updated successfully.";
} else {

    function save_custom_user_profile_fields_points($user_id) {
		/*Turns out this blows up the admin account */
	   // again do this only if you can
        if (!current_user_can('manage_options'))
            return false;

    }

    add_action('user_register', 'save_custom_user_profile_fields_points');
    add_action('profile_update', 'save_custom_user_profile_fields_points');
}



function cgc_ub_action_links($actions, $user_object) {
    $actions['edit_points'] = "<a class='cgc_ub_edit_badges' href='" . admin_url("admin.php?page=vyps_points_list&edituserpoints=$user_object->ID") . "'>" . __('Edit Points') . "</a>";
    return $actions;
}

add_filter('user_row_actions', 'cgc_ub_action_links', 10, 2);

/*** SHORTCODE INCLUDES IN BASE ***/

include( plugin_dir_path( __FILE__ ) . '../VYPS/includes/shortcodes/vypspl.php'); //Point Log
include( plugin_dir_path( __FILE__ ) . '../VYPS/includes/shortcodes/vypsbc.php'); //Balance shortcode
include( plugin_dir_path( __FILE__ ) . '../VYPS/includes/shortcodes/vypsbc_ww.php'); //Balance for woowallet as the built in one annoys me with refresh update
include( plugin_dir_path( __FILE__ ) . '../VYPS/includes/shortcodes/vypspt.php'); //Point Transfer shorcode raw format. Maybe should rename to vypspt_raw.php
include( plugin_dir_path( __FILE__ ) . '../VYPS/includes/shortcodes/vypspt_tbl.php'); //Point Transfer Table code. One day. I'm goign to retire PT, but admins might need it.
include( plugin_dir_path( __FILE__ ) . '../VYPS/includes/shortcodes/vypspt_ww.php'); //WW point transfer bridge Shortcode table
include( plugin_dir_path( __FILE__ ) . '../VYPS/includes/shortcodes/vypslg.php'); //You are not logged in blank shortcode.

/*** End of Shortcode Includes ***/
