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

function vyps_as_sub_menu_page()
{
	//Image URLS
	//NOTE It took me a while to realize, I needed the dirname()
	$VYPS_logo_url = plugins_url( 'images/logo.png', dirname(__FILE__) );

  echo '<br><br><img src="' . $VYPS_logo_url . '" > ';

  //Originally, this was includes to other files but don't need that much de-integration

  echo "<br><br><h1>Adscend Media User Tracking API Shortcodes</h1>
 <p>This plugin needs an <a href=\"https://adscendmedia.com\" target=\"_blank\">Adscend Media</a> account to function. The intention is to allow a quick and easy way for you to award user points for Adscend Activity.</p>
 <p>The intention of this plugin was to make it easier for a WordPress admin to talk with the Adscend API without actually having to program curl calls in PHP manually and automate the recognition of the monetization activity by your end users.</p>
 <h1>Shortcodes and Syntax</h1>
 <p><b>[vyps-as-watch pub=113812 profile=13246 pid=1]</b></p>
 <p>The above shorcode will put up an Adscend wall using the publisher and profile id. (Those are our test site numbers, replace with yours) The pid is the point ID of course.</p>
 <p>The pid is the pointID number seen on the Point List page. This shortcode always requires the user to be logged in and it will always be hardcoded to ID number of the current user.</p>
 <p>To have a user redeem points through the Adscend API (the points Adscend has said they earned). You need to get your own API off your Adscend wall page. The API key is on the integration page on your offer wall under API/SDK integration.</p>
 <p><b>[vyps-as-redeem pub=113812 profile=13246 api=typekeyhere pid=1 payout=750]</b></p>
 <p>All attributes must be set for this to function. This creates a button site user to click on to have your site talk to the Adscend API and confirm how many points they earned from Adscend.</p>
 <h2>Note:</h2>
 <p>If you are not familiar with Adscend Media, they do have odd practices. Users may see ads or do activities but Adscend will not credit them for one reason or another after a while. In my own testing, I noticed I can only get 9 ad watch credits or so in a 24 hour period before it stops paying out on the Adscend side. Switching to another device or IP address helps, but consider telling your users to use our VY256 Miner if they keep hitting their daily view limit as it seems that Adscend may have gotten wise to people leaving their tablet on with volume off running video ads while no one is actually watching.</p>
 <h2>Referral</h2>
 <p>Please help out VidYen when you sign up to Adscend by using our <a href=\"https://www.vidyen.com/adscend-signup/\" target=\"_blank\">referral link</a> on our webpage</p>
 <p>We will also put some tips on getting accepeted on that page as well.</p>
 ";

}
