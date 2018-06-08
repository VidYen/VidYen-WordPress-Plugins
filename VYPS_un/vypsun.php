<?php
/*
  Plugin Name: VYPS Coinhive Addon
  Description: VidYen Point System CoinHive Addon
  Version: 0.0.01
  Author: VidYen, LLC
  Author URI: https://vidyen.com/
  License: GPLv2 or later
 */

 /* 
* Note: Have renamed all tables to the right tables... ie. tables_ch, table_points
* Need to make every variable have specific context so can simply glance at something to know what it does
*
*
 */
 
register_activation_hook(__FILE__, 'vyps_ch_install');

function vyps_ch_install() {
 
	/*
	* Have removed all install as this should not install a table but just clean the tables.
	*/
 
}

add_action('admin_menu', 'vyps_un_submenu', 21 );

/* Creates the Coinhive submenu on the main VYPS plugin */

function vyps_un_submenu() 
{
	$parent_menu_slug = 'vyps_points';
	$page_title = "VYPS DB Uninstall Tool";
    $menu_title = 'VYPS DB Uninstall Tool';
	$capability = 'manage_options';
    $menu_slug = 'vyps_un_page';
    $function = 'vyps_un_sub_menu_page';

    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}


/* this next function creates the page on the Coinhive submenu */

function vyps_un_sub_menu_page() 
{ 
    echo
	"<br><br><img src=\"../wp-content/plugins/VYPS_base/logo.png\">
	<h1>VYPS Base Uninstall Plugin</h1>
	<p>If you are seeing this, it means you have installed the uinstall plugin.</p>
	<p>This is a multipart system similar to WooCommerce as it intends to allow WordPress administrators to create points for monetization and rewards into other system.</p>
	<p>Deactivating and deleting this plugin will delete the user data stored on the WP site.</p>
	<br><br>
	<br><br>
	<h2>Here is a list of our other addons that go along with this system:</h2>
	<ul>
		<li>Coinhive addon plugin</li>
		<li><th>AdScend Plugin</li>
		<li>WooWallet Bridge Plugin</li>
		<li>CoinFlip Game Plugin</li>
		<li>Balance Shortcode Plugin</li>
		<li>Plublic Log Plugin</li>
	</ul>
	";
} 
