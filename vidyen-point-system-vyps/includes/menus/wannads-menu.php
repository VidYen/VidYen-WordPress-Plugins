<?php

//Wannads menu

add_action('admin_menu', 'vyps_wannads_submenu', 420 );

/* Creates the Wannads submenu on the main VYPS plugin */
//Copied and pasted from Adscend and updated for post packs.

function vyps_wannads_submenu()
{
	$parent_menu_slug = 'vyps_points';
	$page_title = "VYPS Wannads Shortcodes";
  $menu_title = 'Wannads Shortcodes';
	$capability = 'manage_options';
  $menu_slug = 'vyps_wannads_page';
  $function = 'vyps_wannads_sub_menu_page';

    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}


/* Below is the functions for the shortcode */

function vyps_wannads_sub_menu_page()
{
	//Image URLS
	//NOTE It took me a while to realize, I needed the dirname()
	$VYPS_logo_url = plugins_url( 'images/logo.png', dirname(__FILE__) );

  echo '<br><br><img src="' . $VYPS_logo_url . '" > ';

  //Originally, this was includes to other files but don't need that much de-integration
  echo '<br><br><h1>Wannads Tracking API Shortcodes</h1>
	<p>This plugin needs a <a href="https://www.wannads.com" target="_blank">Wannads Media</a> account to function. The intention is to allow a quick and easy way for you to award user points for Adscend Activity.</p>
	<p>The intention of this plugin was to make it easier for a WordPress admin to talk with the Adscend API without actually having to program curl calls in PHP manually and automate the recognition of the monetization activity by your end users.</p>
	<h1>Shortcodes and Syntax</h1>
	<p>Display the offer wall on your site.</p>
	<p><b>[vyps-wannads apikey=(found on Wannads site)]</b></p>
	<p>This is pretty much it. You just need to grab your Wannads API and post it here.<p>
	<h1>Post Back Support:</h1>
	<p>As Wannads has no direct redemption API, it is required to create a post back system that is a bit different from everything else up until now.</p>
	<p>You can download the plugin off our <a href="https://www.vidyen.com/product/wannads-postback-plugin/" target="_blank">site store</a></p>
	<p><b>NOTE:</b></p> The plugin is free but we will add you as our referral. Please go ahead and use <a href="https://www.vidyen.com/wannads-signup/" target="_blank">referral code</a> and <a href="https://www.vidyen.com/contact/" target="_blank">let us know</a> when you have been approved.</p>
	<h1>Post Back Instructions:</h1>
	<p>1. Create a WordPress standalone page for the post back.</p>
	<p>2. Install the plugin off the VidYen store.</p>
	<p>3. Add the shortcode: <b>[vyps-wannads-postback outputid=(point ID) secret=(your Wannads secret key)]</b></p>
	<p>4. Make sure no other text or shortcode is in page body. It will cause errors on the Wannads report if you do.</p>
	<p>5. On Edit Page -> Page Attributes (right-hand side on classic editor), set the template to Wannads Post Back Template and update. This will remove all the extra WordPress HTML that would cause the Wannads API to error out on the post back attempt.</p>
	<p><b>Note:</b> VYPS will do a 1-for-1 and round to the closest whole number based on the Wannads settings on their site. Simply set your Wannads Data to more than 100 per US$1 and set their system to currency round.</p>
	<h2>Clarification</h2>
	<p>There must bet two pages!</p>
	<p>One for [vyps-wannads apikey=(found on Wannads site)] which contains the Offer Wall which is visible to users.</p>
	<p>The other for [vyps-wannads-postback outputid=(point ID) secret=(your Wannads secret key)] which only for the Wannads server to see when it communicates with your site.</p>
	<p>The error of INVALID SIGNATURE is actually supposed to be there as only Wannads will be able to see that page and not you. If the post back fails, they will send you an email.</p>
 ';
}
