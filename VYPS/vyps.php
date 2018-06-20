<?php
/*
  Plugin Name: VidYen Point System
  Description: VidYen Point System (VYPS) allows you to gamify monetization by giving your users a reason to turn off adblockers for rewards.
  Version: 0.0.37
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

	/* I intend to figure out what to do about the point_amount data type.
	*  I would like to have some type of decimals for people who use VidYen for and exchange type of system for crypto
	*  But my target audience doesn't care so will figure out a better solution in future.
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

    $page_title = "All Point Adjustments";
    $menu_title = 'All Point Adjustments';
    $menu_slug = 'all_point_adjustments';
    $function = 'vyps_points_all_point_adjustments';
    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}

function vyps_points_all_point_adjustments() {
    global $wpdb;
    $query2 = "select * from {$wpdb->prefix}vyps_points_log order by time desc";
    $point_logs = $wpdb->get_results($query2);
    ?>
    <div class="wrap">        
        <h1 class="wp-heading-inline">All Point Adjustments</h1>        
        <h2>Point Log</h2>
        <table class="wp-list-table widefat fixed striped users">
            <tr>
                <th>Date</th>
                <th>Username - UID</th>
                <th>Point type</th>
                <th>Amount +/-</th>
                <th>Adjustment Reason</th>
            </tr>
            <?php if (!empty($point_logs)): ?>
                <?php $i = 0; ?>
                <?php foreach ($point_logs as $logs): ?>
                    <tr>
                        <td><?= $logs->time; ?></td>
                        <td><?php
                            $userdata = get_userdata($logs->user_id);
                            echo $userdata->data->user_login;
                            ?> -- UID #<?= $logs->user_id; ?></td>
                        <td><?php
                            $points_name = $wpdb->get_row("select * from {$wpdb->prefix}vyps_points where id= '{$logs->points}'");
                            echo $points_name->name;
                            ?></td>
							<? /* Below should be removed later to get rid of the +/- in front of number */ ?>
                        <td><? /* <?= ($logs->adjustment == "plus") ? '+': '-'; ?> */ ?><?= $logs->points_amount; ?></td>
                        <td><?= $logs->reason; ?></td>
                        
                    </tr>
                    <?php $i++; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No data found yet.</td>
                </tr>
            <?php endif; ?>
            <tr>
                <th>Date</th>
                <th>Username - UID</th>
                <th>Point type</th>
                <th>Amount +/-</th>
                <th>Adjustment Reason</th>
            </tr>
        </table>
    </div>
    <?php
}

/*I'm going to rewrite the below into a better advertisment */

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

/*** Ok. After this line we are putting the merged shortcodes ***/

include( plugin_dir_path( __FILE__ ) . '../VYPS/includes/vypspl.php'); 

/*** End of Shortcode Includes ***/
