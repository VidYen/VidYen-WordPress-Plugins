<?php
// Silence is golden, but gold has no intrinsic value.
// At least it's easy to trade digital currencies.

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//adding menues
add_action('admin_menu', 'vyps_points_menu');

function vyps_points_menu()
{
    $parent_page_title = "VidYen Point System";
    $parent_menu_title = 'VidYen';
    $capability = 'manage_options';
    $parent_menu_slug = 'vyps_points';
    $parent_function = 'vyps_points_parent_menu_page';
    add_menu_page($parent_page_title, $parent_menu_title, $capability, $parent_menu_slug, $parent_function);

    $page_title = "Manage Points";
    $menu_title = 'Point List';
    $menu_slug = 'vyps_point_list';
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
include( plugin_dir_path( __FILE__ ) . 'includes/menus/core_shortcodes_menu.php'); //Core shortcodes. Will be just deemed VYPS Shortcodes for menu's sake. Order 360
include( plugin_dir_path( __FILE__ ) . 'includes/menus/as_menu.php'); //Adscend menu 400 order
include( plugin_dir_path( __FILE__ ) . 'includes/menus/vy256-menu.php'); //CH menu 366 order
include( plugin_dir_path( __FILE__ ) . 'includes/menus/wannads-menu.php'); //CH menu 420 order

/*** End of Menu Includes ***/

/* Main page informational page. Includes shortcodes, advertistments etc */

function vyps_points_parent_menu_page()
{
	//It's possible we don't use the VYPS logo since no points.
  $vyps_logo_url = plugins_url( 'includes/images/logo.png', __FILE__ );
	//Logo from base. If a plugin is installed not on the menu they can't see it not showing.
	echo '<br><br><img src="' . $vyps_logo_url . '" > ';

	//Static text for the base plugin
	echo
	"<h1>VidYen Point System Base Plugin</h1>
	<p>VYPS allows you to gamify monetization by giving your users a reason to turn off adblockers in return for rewards and recognition.</p>
	<p>This is a multi-part system, similar to WooCommerce, that allows WordPress administrators to track points for rewards using monetization systems.</p>
	<p>To prevent catastrophic data loss, uninstalling this plugin will no longer automatically delete the VYPS user data. To drop your VYPS tables from the WPDB, use the VYPS Uninstall plugin to do a clean install.</p>
	<br>
	<h2>Base Plugin Instructions</h2>
	<p>Navigate to the Add Points menu to add points.</p>
	<p>Go to the Users panel and use the context menu by Edit User Information under Edit Points to modify or see a user’s current point balance.</p>
	<p>Go to Point Log in the VidYen Points menu to see a log of all user transactions.</p>
	<p>See the shortcode menus on how to integrate this on your WordPress site.</p>
	";

	include( plugin_dir_path( __FILE__ ) . 'includes/menus/credits.php');
	//plugins_url( 'includes/menus/credits.php', __FILE__ );

}

function vyps_points_sub_menu_page()
{
    global $wpdb;
    require plugin_dir_path(__FILE__) . 'manage_points.php';
}

function vyps_points_add_sub_menu_page()
{
    global $wpdb;
    require plugin_dir_path(__FILE__) . 'add_point.php';
}
