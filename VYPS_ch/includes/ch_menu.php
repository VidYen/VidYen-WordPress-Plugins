<?php

//Menu to show the shortcode instructions.

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

 add_action('admin_menu', 'vyps_ch_submenu', 430 );

/* Creates the Coinhive submenu on the main VYPS plugin */


function vyps_ch_submenu()
{
  $parent_menu_slug = 'vyps_points';
  $page_title = "Manage Coinhive API";
  $menu_title = 'Coinhive Settings';
  $capability = 'manage_options';
  $menu_slug = 'vyps_ch_page';
  $function = 'vyps_ch_sub_menu_page';

    add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}


/* this next function creates the page on the Coinhive submenu */

function vyps_ch_sub_menu_page() {

  //Seeing if plug in active else give its own menu.
  if ( is_plugin_active( 'vidyen-point-system-vyps/vyps.php' ) ) {

    /* Getting the plugin root path. I'm calling VYPS_root but not to be confused with the root in the folder */
  	$VYPS_root_path = plugin_dir_path(__FILE__);
  	$path_find = "VYPS_ch/includes/";
  	$path_remove = '';
  	$VYPS_root_path = str_replace( $path_find, $path_remove, $VYPS_root_path);

  	$VYPS_logo_url = plugins_url() . '/vidyen-point-system-vyps/images/logo.png'; //I should make this a function.

  	echo '<br><br><img src="' . $VYPS_logo_url . '" > ';

    echo "
    	<div class=\"wrap\">
    		<h1 >VYPS Coinhive Simple Miner API Plugin Addon</h1>
    		<p>This addon creates a method for you to have users mine Coinhive hashes and have the Coinhive and VYPS api show how many hashes they mined and redeemed on your site.</p>
        <p>The intention is to let users mine Coinhive hashes and let your site automatically award points for them which can be used for site activities, games, or transferred into the WooCommerce Wallet so that they can purchase digital items via mining Coinhive hashes.</p>
        <p>Or you can just have mine for personal recognition via the leaderboard addon which shows who mined the most if this was a charity page for example.</p>
        <h1>Shortcodes:</h1>
        <p><b>NOTE:</b> To display the simple miner you need to place the consent button on your page to create a post that the simple miner shortcode will recognize.</p>
        <p>It is up to you to come up with a consent message, but you need to inform the user what Coinhive is and what it can do resource and battery wise to their device. (Feel free to use the same message on the VidYen official page).
        <p><b>The consent and simple miner shortcode must be put on the same page to work!</b></p>
        <br>
        <p><b>[vyps-ch-consent txt=(optional)]</b></p>
        <p>Display the simple miner consent button. You can customize the text on the button by using txt= and can have spaces in the button text by using quotes if needed.</p>
        <p><b>[vyps-ch-sm skey=(required) pkey=(required) pid=(required) suid=(optional) threads=(optional) throttle=(optional)]</b></p>
        <p>This will display the simple miner after the button on the consent shortcode has been pressed.</p>
        <p>skey: Coinhive Site Key</p>
        <p>pkey: Coinhive Private Key Key</p>
        <p>pid: The VYPS point ID of the point type you want to redeem too (NOTE: Hashes are always redeemed for a 1 for 1. You can use the point exchange shortcode to convert to different amounts before transfering to WooCommerce.)</p>
        <p>suid: Is the name of the site in case you wan to run different miners but use same site key to differentiate users.</a>
        <p>threads: Default is 1 to only mine one thread. I wuld recommend it leaving as such in case your users complain about slow devices.</p>
        <p>throttle: The defualt is 90 so it reduces CPU use by 90% so they only use 10% for mining. I would also recommend leaving this as such and let the user decide to increase if they want to mine faster via the simple miner GUI.</p>
        <p>example: [vyps-ch-sm skey=5y8ys1vO4guiyggOblimkt46sAOWDc8z pkey=A6YSYjxSpS0NY6sZiBbtV6qdx4006Ypw pid=2 suid=FooYen] Please consent to mining. [vyps-ch-consent]</p>
        <p>This will show the miner after the consent button has been clicked.</p>
        <h1>Support</h1>
        <p>Although the you will need to contact Coinhive is there is a problem on their side, we are familiar with issues with them, so please visit <a href=\"https://www.vidyen.com/about/\" target=\"_blank\">VidYen About</a> for the current method of contacting us.</p>
        <p>We would like Coinhive to take off as a legitimate monetization technology so before uinstalling out of fustraction, please contact us and we try to help out to get this working!</p>
    	</div>
      ";

  	/* I may not want advertising, but I suppose putting it here never hurts */
  	$credits_include = $VYPS_root_path . 'vidyen-point-system-vyps/includes/credits.php';

    include( $credits_include );

  } else {

    function vyps_ch_menu()
    {

        $parent_page_title = "Coinhive Addon: VYPS not installed!";
        $parent_menu_title = 'VYPS Coinhive';
        $capability = 'manage_options';
        $parent_menu_slug = 'vyps_ch';
        $parent_function = 'vyps_ch_parent_menu_page';
        add_menu_page($parent_page_title, $parent_menu_title, $capability, $parent_menu_slug, $parent_function);
    }


    /* this next function creates the page on the Coinhive submenu */

    function vyps_ch_parent_menu_page()
    {
    	/* I'm putting the logo at top because I can */
    	//echo '<br><br><img src="' . plugins_url( '../VYPS/images/logo.png', __FILE__ ) . '" > '; //It dawned on me that if they don't have VYPS installed they can't see the credits.

    	echo "  <br><br>
    			<b>***Warning***</b><br><br>It does not appear you have the VYPS base plugin installed!
    			Please visit <a href=\"https://www.vdiden.com/vyps/\" target=\"_blank\">VidYen</a> to download or search for \"VYPS\" on Wordpress.org to install.<br>
    			After install, deactivate and reactivate this plugin.
    			";


    	/* I may not want advertising, but I suppose putting it here never hurts */
    	//include( plugin_dir_path( __FILE__ ) . '../VYPS/includes/credits.php'); 	//Also the addvertising won't work either if VYPS not installed.
    }

  }



}
