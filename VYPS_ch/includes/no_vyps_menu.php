<?php
/*
   This menu shows when user does not have VYPS base installed for some odd reason. They should know, but sometimes you never know.
 */

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

 add_action('admin_menu', 'vyps_ch_menu'); //Note dropping the number so I think it should sort alpha

/* Create the menu if VYPS not installed */

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
