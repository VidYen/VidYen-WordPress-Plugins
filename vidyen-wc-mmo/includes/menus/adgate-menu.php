<?php

//AdGate menu

add_action('admin_menu', 'vyps_adgate_submenu', 450 ); //Last VYPS was 440

/* Creates the AdGate submenu on the main VYPS plugin */
//Copied and pasted from Adscend and updated for post packs.

function vyps_adgate_submenu()
{
	$parent_menu_slug = 'vyps_points';
	$page_title = "VYPS AdGate Shortcodes";
  $menu_title = 'AdGate Shortcodes';
	$capability = 'manage_options';
  $menu_slug = 'vyps_adgate_page';
  $function = 'vyps_adgate_sub_menu_page';

    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}


/* Below is the functions for the shortcode */

function vyps_adgate_sub_menu_page()
{
	//Image URLS
	//NOTE It took me a while to realize, I needed the dirname()
	$VYPS_logo_url = plugins_url( 'images/logo.png', dirname(__FILE__) );

  echo '<br><br><img src="' . $VYPS_logo_url . '" > ';

  //Originally, this was includes to other files but don't need that much de-integration
  echo '<br><br><h1>AdGate User Tracking API Shortcodes</h1>
	<p>This plugin needs a <a href="https://adgatemedia.com/" target="_blank">AdGate Media</a> account to function. The intention is to allow a quick and easy way for you to award user points for Adscend Activity.</p>
	<p>The intention of this plugin was to make it easier for a WordPress admin to talk with the Adscend API without actually having to program curl calls in PHP manually and automate the recognition of the monetization activity by your end users.</p>
	<h1>Shortcodes and Syntax</h1>
	<p><b>[vyps-adgate code=(found on AdGate site)]</b></p>
	<p>ex: <b>[vyps-adgate code=nqaaqg]</b></p>
	<p>This is pretty much it. Just need to grab your AdGate code off the AdGate <a href="https://panel.adgatemedia.com/affiliate/vc-walls" target="_blank">rewards page</a> to shortcode.<p>
	<h1>Post Back Instructions</h1>
	<p>1. Create a WordPress standalone page for the post back.</p>
	<p>2. Add the shortcode: <b>[vyps-adgate-postback outputid=(point id) secret=(your AdGate secret key)]</b> (ex: [vyps-adgate-postback outputid=7])</p>
	<p>3. Make sure no other text or shortcode is in page body. It will cause errors on the AdGate report if you do.</p>
	<p>4. On Edit Page -> Page Attributes (right-hand side on classic editor), set the template to AdGate Post Back Template and update. This will remove all the extra WordPress HTML that would cause the AdGate API to error out on the post back attempt.</p>
	<p><b>Note:</b> VYPS will do a 1-for-1 and round to the closest whole number based on the AdGate settings on their site. Simply set your AdGate Data to more than 100 per US$1 and set their system to currency round.</p>
	<p>TThere are optional shortcodes. By default, the AdGate post back whitelist IPs are 104.130.7.162 and 52.42.57.125, but if they are different, use ip1=, ip2=, and ip3= to set your specific addresses.</p>
	<p>Also, <b>reason="My Own Reason"</b> will set a specific reason for the log system. By default, it is "AdGate", Aalthough I use the Global Postback: <b>https://vidyen.com/fabius/adgate-postback/?tx_id={transaction_id}&user_id={s1}&point_value={points}&usd_value={payout}&offer_title={vc_titlpoe}&point_value={points}&status={status}</b> you may wish to edit your own, but the following are required: <b>{user_id}</b>, <b>{tx_id}</b>, <b>{point_value}</b>, <b>{status}</b></p>
	<h2>API Security</h2>
	<p>Due to the only security built into AdGate, I added a manual api system. Add <b>apikey=(your random number)</b> to the shortcode.</p>
	<p>Then on the post back page of AdGate add the &api= key as such:</p>
	<p>https://vidyen.com/fabius/adgate-postback/?tx_id={transaction_id}&user_id={s1}&point_value={points}&usd_value={payout}&offer_title={vc_titlpoe}&point_value={points}&status={status}&api=(your random number again)</p>
	<h2>Referral</h2>
	<p>lease help out VidYen when you sign up to Adscend by using our <a href="https://panel.adgatemedia.com/r/62694" target="_blank">referral link</a> on our webpage</p>
	<p>We will also put some tips on getting accepted on that page as well.</p>
 ';
}
