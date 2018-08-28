<?php

//Adscend menu

add_action('admin_menu', 'vyps_adscend_submenu', 400 );

/* Creates the Adscend submenu on the main VYPS plugin */

function vyps_adscend_submenu()
{
	$parent_menu_slug = 'vyps_points';
	$page_title = "VYPS Adscend Shortcodes";
    $menu_title = 'Adscend Shortcodes';
	$capability = 'manage_options';
    $menu_slug = 'vyps_as_page';
    $function = 'vyps_as_sub_menu_page';

    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}


/* Below is the functions for the shortcode */

function vyps_as_sub_menu_page() {

  $VYPS_logo_url = plugins_url() . '/vidyen-point-system-vyps/images/logo.png'; //I should make this a function.

  echo '<br><br><img src="' . $VYPS_logo_url . '" > ';

  //Originally, this was includes to other files but don't need that much de-integration

  echo "<br><br><h1>VYPS Adscend Media User Tracking API Shortcodes</h1>
 <p>This plugin needs an <a href=\"https://adscendmedia.com\" target=\"_blank\">Adscend Media</a> account to function. The intention is to allow a quick and easy way for you to award user points for Adscend Activity.</p>
 <p>The intention of this plugin was to make it easier for a WordPress admin to talk with the Adscend API without actually having to program curl calls in PHP manually and automate the recognition of the monetization activity by your end users.</p>
 <h1>Shortcodes and Syntax</h1>
 <p><b>[vyps-as-watch pub=113812 profile=13246 pid=1]</b></p>
 <p>The above shorcode will put up an Adscend wall using the publisher and profile id. (Those are our test site numbers, replace with yours) The pid is the point ID of course.</p>
 <p>The pid is the pointID number seen on the Point List page. This shortcode always requires the user to be logged in and it will always be hardcoded to ID number of the current user.</p>
 <p>To have a user redeem points through the Adscend API (the points Adscend has said they earned). You need to get your own API off your Adscend wall page. The API key is on the integration page on your offer wall under API/SDK integration.</p>
 <p><b>[vyps-as-redeem pub=113812 profile=13246 api=typekeyhere pid=1 payout=750]</b></p>
 <p>All attributes must be set for this to function. This creates a button site user to click on to have your site talk to the Adscend API and confirm how many points they earned from Adscend.</p>
 ";

}
