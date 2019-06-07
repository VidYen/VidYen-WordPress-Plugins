<?php

//Improved shortcode of public log.


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Function removed and moved to function folder.
//include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_public_log_func.php'); //Functionalized public log (This should have been months ago! -Felty)

//Shortcode for the log.

add_shortcode( 'vyps-pl', 'vyps_public_log_func');

add_shortcode( 'vidyen-point-log', 'vyps_public_log_func'); //note I'm slowly chaning naming convention. Havin these two side by side should keep from breaking people's sites.

add_shortcode( 'vidyen-user-log', 'vyps_user_log_func'); //note I'm slowly chaning naming convention. Havin these two side by side should keep from breaking people's sites.
