<?php

//Menu to show the shortcode instructions.
//NOTE: I have decided to move this plugin menu to the official as it's just shorcode

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//add_action('admin_menu', 'vyps_coinhive_submenu', 430 ); //Commented out. Short codes still will work. 2.11.19 -Felty I'm tired of supporting centralize serverices

/* Creates the Coinhive submenu on the main VYPS plugin */

function vyps_coinhive_submenu() {

  $parent_menu_slug = 'vyps_points';
  $page_title = "VYPS Coinhive Shortcodes";
  $menu_title = 'Coinhive Shortcodes';
  $capability = 'manage_options';
  $menu_slug = 'vyps_coinhive_page';
  $function = 'vyps_coinhive_sub_menu_page';

    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}


/* this next function creates the page on the Coinhive submenu */

function vyps_coinhive_sub_menu_page()
{
  //Image URLS
  //NOTE It took me a while to realize, I needed the dirname()
  $VYPS_logo_url = plugins_url( 'images/logo.png', dirname(__FILE__) );
  $VYPS_worker_url = plugins_url( 'images/vyworker_small.gif', dirname(__FILE__) );

	echo '<br><br><img src="' . $VYPS_logo_url . '" > ';

  echo '
  <div class="wrap">
    <h1>Coinhive Simple Miner User Tracking API Shortcodes</h1>
    <p>This addon creates a method for you to have users mine Coinhive hashes and have the Coinhive and VYPS API show how many hashes they mined and redeemed on your site.</p>
    <p>The intention is to let users mine Coinhive hashes and allow your site to automatically award points. These points can be used for site activities, games, or transferred into the WooCommerce Wallet so that they can purchase digital items via mining Coinhive hashes.</p></p>
    <p>Or you can have them mine for personal recognition via the leaderboard addon, which shows who mined the most (for example, if this is being used on a charity page).</p>
    <p>This requires you to sign up for a free <a href="https://coinhive.com/" target="_blank">Coinhive</a> account and a Monero wallet of your choice for payout.</a>
    <p><b>NOTE:</b> Use of Coinhive will also cause some anti-virus and anti-malware programs to respond. This is for good reason as the same technology can be used silently and without consent.</p>
    <p>You should make users aware that this may happen in your consent disclaimer and let them know they could receive an alert from their security software.</p>
    <p>That said, legitimate organizations like <a href="https://www.thehopepage.org/" target="_blank">UNICEF</a> are using Coinhive for donations, so like any piece of technology, it can be used for both legal and illegal purposes.</p>
    <p>If you are concerned about the issues of having Coinhive on your site, we recommend using the Adscend shortcode to use traditional advertisements for site monetization or use the VY256 Miner which is not flagged by Adblockers or Malwarebytes.</p>
    <p>Also, as long as you do not put the Coinhive shortcode on your site, it will not expose your users to any code from Coinhive as no code is called until the accept button is clicked.</p>
    <p><b>NOTE:</b> To display the simple miner, you need to place the consent button shortcode on your page to create a POST call that the simple miner shortcode will recognize.</p>
    <p>It is up to you to come up with a consent message, but you need to inform the user what Coinhive is and what it may do resource- and battery-wise to their device. (Feel free to use the same message that is on the VidYen official page.)</p>
    <p><b>The consent and simple miner shortcode must be put on the same page to work.</b></p>
    <p><b>Users must be logged in to see any of these shortcodes as the intent was to track user effort.</b></p>
    <h1>Shortcodes and Syntax</h1>
    <h2>Consent Button Shortcode</h2>
    <p><b>[vyps-ch-consent txt=(optional)]</b></p>
    <p>Display the simple miner consent button. You can customize the text on the button by using txt=.To add spaces in the button text, use quotes.</p>
    <h2>Simple Miner Display and User Tracking Shortcode</h2>
    <p><b>[vyps-ch-sm skey=(required) pkey=(required) pid=(required) suid=(optional) threads=(optional) throttle=(optional)]</b></p>
    <p>This will display the simple miner after the button on the consent shortcode has been pressed.</p>
    <p>skey: Coinhive Site Key</p>
    <p>pkey: Coinhive Private Key</p>
    <p>pid: The VYPS point ID found in the “VYPS Point List” of the point type you want to redeem to.</p>
    <p>NOTE: Hashes are always redeemed for a 1-for-1. You can use the point exchange shortcode to convert to different amounts before transferring to WooCommerce.</p>
    <p>suid: The name of the site in case you want to run different miners but use same site key to differentiate users.</p>
    <p>threads: Default is 1 to only mine one thread. We would recommend leaving it set to 1 in the event your users have slow devices.</p>
    <p>throttle: The default is 90, which reduces CPU use by 90%, so they only use 10% for mining. I would also recommend leaving this set to 90 and allowing the user to increase it if they want to mine faster via the simple miner GUI.</p>
    <p>Example:</p>
    <p><b>[vyps-ch-sm skey=5y8ys1vO4guiyggOblimkt46sAOWDc8z pkey=A6YSYjxSpS0NY6sZiBbtV6qdx4006Ypw pid=2 suid=FooYen] Please consent to mining. [vyps-ch-consent]</b></p>
    <p>This will show the miner after the consent button has been clicked.</p>
    <h1>Support</h1>
    <p>Although you will need to contact Coinhive if there is a problem on their side, we are familiar with Coinhive API issues, so please visit <a href="https://www.vidyen.com/about/" target="_blank">VidYen About</a> or our WordPress support page for the current method of contacting us.</p>
    <p>We would like Coinhive to take off as a legitimate monetization technology, so before uninstalling out of frustration, please contact us, and we will try to help out.</p>
  </div>
    ';

  	/* I may not want advertising, but I suppose putting it here never hurts */
  	//$credits_include = $VYPS_root_path . 'includes/credits.php';

}
