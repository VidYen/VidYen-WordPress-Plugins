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
  $VYPS_logo_url = plugins_url() . '/vidyen-point-system-vyps/images/logo.png'; //I should make this a function.

  echo '<br><br><img src="' . $VYPS_logo_url . '" > ';

  //Originally, this was includes to other files but don't need that much de-integration
  echo '<br><br><h1>VYPS Adscend Media User Tracking API Shortcodes</h1>
	<p>This plugin needs a <a href="https://www.wannads.com" target="_blank">Wannads Media</a> account to function. The intention is to allow a quick and easy way for you to award user points for Adscend Activity.</p>
	<p>The intention of this plugin was to make it easier for a WordPress admin to talk with the Adscend API without actually having to program curl calls in PHP manually and automate the recognition of the monetization activity by your end users.</p>
	<h1>Shortcodes and Syntax</h1>
	<p><b>[vyps-wannads apikey=(found on Wannads site)]</b></p>
	<p>This is pretty much it. Just need to grab your Wannads API and post here<p>
	<h1>Post Back Support:</h1>
	<p>As Wannads has no direct redemption API, it was required to create a post back system which is a bit different that everything else up until now.</p>
	<p>You can download the plugin off our <a href="https://www.vidyen.com/product/wannads-postback-plugin/" target="_blank">site store</a></p>
	<p><b>NOTE:</b></p> While you can pay us the $10 with PayPal for the download, you can simply use our referral code and <a href="https://www.vidyen.com/contact/" target="_blank">let us know</a> when you have been approved, and will will manually send you the $10 in credit.</p>
	<p>Alternatively, you can just use one of the monetization methods to earn the WooCommerce Credit just like a user would on your site.</p>
	<h1>Post Back Instructions:</h1>
	<p>1. Create a WordPress stand alone page for the post back.</p>
	<p>2. Install the plugin off the VidYen store.</p>
	<p>3. Add the shortcode: <b>[vyps-wannads-postback outputid=(point id) secret=(your Wannads secret key)]</b></p>
	<p>4. Make sure no other text or shortcode is in page body. It will cause errors on the Wannads report if you do.</p>
	<p>5. On Edit Page -> Page Attributes (right hand side on classic editor), set the template to Wannads Post Back Template and update. This will remove all the extra WordPress HTML that would cause the Wannads API to error out on the post back attempt.</p>
	<p><b>Note:</b> VYPS will do a 1 for 1 and round to the closet whole number based on the Wannads settings on their site. Simply set your Wannads Data to more than 100 per $1.00 USD and set their system to currency round.</p>
 ';
}
