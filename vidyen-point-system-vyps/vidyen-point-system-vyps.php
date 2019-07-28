<?php
/*
Plugin Name:  VidYen Crypto Reward System
Plugin URI:   https://wordpress.org/plugins/vidyen-point-system-vyps/
Description:  Reward users for web mining crypto, watching video ads, or other money making activities on your site.
Version:      3.0.0
Author:       VidYen, LLC
Author URI:   https://vidyen.com/
License:      GPLv2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

/*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, version 2 of the License
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* See <http://www.gnu.org/licenses/>.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Ok. I'm adding a custom fuction to the VYPS plugin. It's put on pages where, you just want to straight kick people out if they aren't admins.
//Similar to the login check, but the admins. This will put put on all pages that only admins should be able to see but not the shortcodes results.

function VYPS_check_if_true_admin()
{
	//I'm going to be a little lenient and if you can edit users maybe you should be able to edit their point since you can just
	//Change roles at that point. May reconsider.
	if( current_user_can('install_plugin') OR current_user_can('edit_users') )
	{
		//echo "You good!"; //Debugging
		return;
	}
	else
	{
		echo "<br><br>You need true administrator rights to see this page!"; //Debugging
		exit; //Might be a better solution to iform before exit like an echo before hand, but well....
	}
}

register_activation_hook(__FILE__, 'vyps_points_install');

//Install the SQL tables for VYPS.
function vyps_points_install()
{
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
		game_id tinytext NOT NULL,
		PRIMARY KEY  (id)
        ) {$charset_collate};";

		$table_name_wm = $wpdb->prefix . 'vidyen_wm_settings';

		$sql .= "CREATE TABLE {$table_name_wm} (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		button_text TINYTEXT NOT NULL,
		disclaimer_text MEDIUMTEXT NOT NULL,
		eula_text MEDIUMTEXT NOT NULL,
		login_text MEDIUMTEXT NOT NULL,
		login_url varchar(256) NOT NULL,
		current_wmp varchar(256) NOT NULL,
		current_pool varchar(256) NOT NULL,
		site_name varchar(256) NOT NULL,
		crypto_wallet varchar(256) NOT NULL,
		hash_per_point double(64, 0) NOT NULL,
		point_id varchar(11) NOT NULL,
		graphic_selection varchar(256) NOT NULL,
		wm_pro_active BOOL NOT NULL,
		wm_woo_active BOOL NOT NULL,
		wm_threads TINYINT NOT NULL,
		wm_cpu TINYINT NOT NULL,
		discord_webhook varchar(256) NOT NULL,
		discord_text MEDIUMTEXT NOT NULL,
		youtube_url varchar(256) NOT NULL,
		custom_wmp varchar(256) NOT NULL,
		PRIMARY KEY  (id)
				) {$charset_collate};";

    require_once (ABSPATH . 'wp-admin/includes/upgrade.php'); //I am concerned that this used ABSPATH rather than the normie WP methods

    dbDelta($sql);

		$site_disclaim_name = get_bloginfo('name');

		$default_disclaimer_text = "By clicking the button you consent to have your browser mine cryptocurrency and to exchange it with $site_disclaim_name for points. This will use your device’s resources, so we ask you to be mindful of your CPU and battery use.";
		$default_login_text = "You need to be logged in to get credit for Webmining!";

		//Default data
		$data_insert = [
				'button_text' => 'I agree and consent',
				'disclaimer_text' => $default_disclaimer_text,
				'eula_text' => '',
				'login_text' => $default_login_text,
				'current_wmp' => 'igori.vy256.com:8256',
				'current_pool' => 'moneroocean.stream',
				'site_name' => 'default',
				'crypto_wallet' => '',
				'hash_per_point' => 256,
				'point_id' => 1,
				'graphic_selection' => 'girl=1&guy=1&cyber=1&undead=1&peasant=1&youtube=0', //Array of the graphics.
				'wm_pro_active' => 0,
				'wm_woo_active' => 0,
				'wm_threads' => 2,
				'wm_cpu' => 100,
				'discord_webhook' => '',
				'discord_text' => 'Hey everyone! User [user], earned [amount] in credit for mining! :pick:',
				'youtube_url' => '',


		];

		$wpdb->insert($table_name_wm, $data_insert);
}

//Updated on 11.14.2018
function vyps_admin_log()
{

	//Shortcode hard coding for the admin log. Yes, it is missing the actual user name (has the UID though) this should suffice
	$atts = array(
		'pid' => '0',
		'reason' => '0',
		'rows' => 50,
		'bootstrap' => 'no',
		'userid' => '0',
		'uid' => TRUE,
		'admin' => TRUE,
	);

	//Echo and not return due to the nature of this not being a shortcode and a page.
	echo vyps_public_log_func( $atts );
}

//NOTE: I moved the menus to its own php file to make this easier to use.

//start add new column points in user table
//BTW I prefixed the next two functions with vyps_ as I have a feeling that might be used by other plugins
//Since it was generic
function vyps_register_custom_user_column($columns)
{
    $columns['points'] = 'Points';
    return $columns;
}

/* The next function is important to show the points in the user table */
function vyps_register_custom_user_column_view($value, $column_name, $user_id)
{
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
function vyps_user_menu_action_links($actions, $user_object)
{
		//Ok. The nonce.
		$vyps_nonce_check = wp_create_nonce( 'vyps-nonce' );
    $actions['edit_points'] = "<a class='cgc_ub_edit_badges' href='" . admin_url("admin.php?page=vyps_point_list&edituserpoints=$user_object->ID&_wpnonce=$vyps_nonce_check") . "'>" . __('Edit Points') . "</a>";
    return $actions;
}

add_filter('user_row_actions', 'vyps_user_menu_action_links', 10, 2);

/*** MENU Includes INCLUDES ***/
include( plugin_dir_path( __FILE__ ) . 'vidyen-point-system-menu.php'); //Menu ads. Not actually in the includes folder.

/*** SHORTCODE INCLUDES IN BASE ***/

//It has dawned on me that the ../vidyen-point-etc may not be needed actually?

//include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/debug.php'); //We got so complicated needed to help users troubleshoot server errors. Left off when not needed but will make more detailed later.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-point-log.php'); //Point Log
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypsbc.php'); //Balance shortcode
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypsbc_ww.php'); //Balance for woowallet as the built in one annoys me with refresh update
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypspt.php'); //Point Transfer shorcode raw format. Maybe should rename to vypspt_raw.php
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypspt_tbl.php'); //Point Transfer Table code. One day. I'm goign to retire PT, but admins might need it.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypspt_2in.php'); //Point Transfer with two inputs.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-point-exchange.php'); //Point Exchange is going to depreciate all earlier versions of Point Transfer
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypspt_ww.php'); //WW point transfer bridge Shortcode table
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-login.php'); //You are not logged in blank shortcode.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-adscend.php'); //Rolling the Adscend in. I hate ads but I'm being pragmatic
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-wannads.php'); //Adding Wannads support. Not the naming convention change.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-wannads-postback.php'); //Wannads post back
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypstr.php'); //Threshold Raffle shortcode. This is going to be cool
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypstr_cl.php'); //Current game log so you can see progress. Need to work on a game history log.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-public-balance.php'); //Point balances for public viewing (and maybe some leaderboard stuff)
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-public-balance-earned.php'); //Point balances of lifetime earnings for public viewing (and maybe some leaderboard stuff)
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-vy256.php'); //VYPS WMP shortcode
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypsxmr_wallet.php'); //Let's user add XMR wallet to usermeta table in WP
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vypsws_pick.php'); //Shareholder pick. Is shortcode but used elsewhere
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-refer.php'); //Referal shortcode display shortcode.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps_refer_balance.php'); //Referal balance shortcdoe. I really need to functionize this.
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-quads.php'); //QUADS the game. Moving to a new tomorrow!
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-reward-timers.php'); //QUADS the game. Moving to a new tomorrow!

/*** End of Shortcode Includes ***/

/*** FUNCTION INCLUDES***/

/*** CORE ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_procheck_func.php'); //Pro checking.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_wannads_pro_func.php'); //Wannads Pro checking.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_balance_func.php'); //Functionalized balance (FINALLY! -Felty)
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_public_log_func.php'); //Functionalized public log (This should have been months ago! -Felty)
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vidyen_user_log_func.php'); //Log for just the user. This was a long time coming. I may had some gets.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vidyen_public_log_func.php'); //New log for public use. Should be way more SQL efficient. Not there is the old so it won't break old version by upgrading (will be depreciated)
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vidyen_user_display_name_func.php'); //Log for just the user. This was a long time coming. I may had some gets.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_add_func.php'); //Functionalized adds to the log
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_deduct_func.php'); //Functionalized deducts to the log
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_meta_check_func.php'); //Meta checking. See if there is a duplicate transaction.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_point_name_func.php'); //Functionalized point name and icon calls (FINALLY! -Felty)
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_point_icon_func.php'); //Functionalized point name and icon calls (FINALLY! -Felty)
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_point_icon_url_func.php'); //Need to get the url just by itself.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_point_balance_func.php'); //I neeced a raw balance function
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_point_earned_func.php'); //More for the MMO but it should go here since sites could use it.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_point_credit_func.php'); //Streamlined credit
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_point_deduct_func.php'); //Streamlined deduct
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_point_debug_func.php'); //Debug mode. Reduces memory footprint of miner.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_point_timer.php'); //Timer moved to its own function since more than one short code using it
include( plugin_dir_path( __FILE__ ) . 'includes/functions/wm/vidyen_wm_settings.php'); //Timer moved to its own function since more than one short code using it

/*** VYVM ***/
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-wm.php'); //Going to contain this here on these few lines.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/wm/vidyen_wm_shortcode_func.php'); //The php code itself.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/wm/vidyen_wm_set_cookie_action.php'); //The php code itself.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/wm/vidyen_wm_ajax.php'); //The new improved auto workings
include( plugin_dir_path( __FILE__ ) . 'includes/functions/wm/vidyen_wm_discord_webhook.php'); //the improved webhook

/*** MO API ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/mo-api/vyps_mo_price_api.php'); //MO API functions. may break into different currencies for but now
/*** REFER ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/refer/vyps_current_refer_func.php'); //General function to check if current user has a refer set, is valid, and returns it.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/refer/vyps_current_refer_name_func.php'); //I needed a function to tell who is the current refer since we needed it for multi device mining.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/refer/vyps_create_refer_func.php'); //Function to create encode based off user id.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/refer/vyps_is_refer_func.php'); //Function to make sure it is really a refer.

/*** WALLET ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/wallet/vyps_dashed_slug_bal_check_func.php'); //Tie in to Dashed Slug's wallet to check if transfer balance is possible
include( plugin_dir_path( __FILE__ ) . 'includes/functions/wallet/vyps_dashed_slug_move_func.php'); //Function to transfer points between users if points allow.

/*** WooWallet ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/ww/vyps_woowallet_credit_func.php'); //Function to credit the WooWallet.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/ww/vyps_woowallet_debit_func.php'); //Function to debit the WooWallet.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/ww/vyps_woowallet_bal_func.php'); //Function to check bal the WooWallet.
include( plugin_dir_path( __FILE__ ) . 'includes/functions/ww/vidyen_woowallet_credit_func.php'); //Credit for the ajax system. More modern version
include( plugin_dir_path( __FILE__ ) . 'includes/functions/ww/vyps_ww_point_bal_func.php'); //Updated balance feature. Can be used more than the get current user

/*** AJAX ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/ajax/vyps_ajaxurl.php'); //Forces ajax to be called regardless of installation
include( plugin_dir_path( __FILE__ ) . 'includes/functions/ajax/vyps_mo_ajax.php'); //MO Pull ajax

/*** XMR Wallet Check ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/xmr-wallet/vyps_xmr_wallet_check.php'); //Function to make sure wallet is valid

/*** End of Function Includes ***/
