<?php

//Balance shortcode revision.
//I realized that I should give more power to the admins in their flexibility so made shortcodes to call specific pointIDs and user IDs.
//Scrapping the old code as it was terrible.

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//NOTE: Code has been moved to function/core/
//include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_balance_func.php'); //Functionalized balance (FINALLY! -Felty)

/* Send shortcode into WP */
add_shortcode( 'vyps-balance', 'vyps_balance_func');
