<?php
 /*
Plugin Name:  VidYen User Market
Plugin URI:   https://wordpress.org/plugins/vidyen-point-system-vyps/
Description:  Allows users to transfer points betwen each other.
Version:      0.0.1
Author:       VidYen, LLC
Author URI:   https://vidyen.com/
License:      GPLv2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

/*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, version 2 of the License
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* See <http://www.gnu.org/licenses/>.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Don't really need SQL for this one but just vyps

/*** Includes ***/
/*** Functions ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vidyen_mmo_woocommerce_check_func.php'); //Checks to see if WooCommerce installed, run first
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_woowallet_currency.php'); //Custom Currencies to WooCommerce
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_sql_call_func.php'); //SQL Call functions
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_mmo_wc_ww_bal_func.php'); //Custom WooWallet balance function for this purpose
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vidyen_mmo_loa_user_query_func.php'); //Adds meta check

/*** Shortcodes ***/
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-mmo-bal.php'); //Ajax Balance
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-wc-mmo-point-exchange.php'); //Ajax Point Exchange
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-mmo-credit-postback.php'); //Post back for game credit transfers
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-mmo-deduct-postback.php'); //Post back for game deduct transfers
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-mmo-api-bal.php'); //Post back for game transfers
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-mmo-loa-id.php'); //Stores the LOA Player ID
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-mmo-register.php'); //Registers the User ID

/*** Menu Includes ***/
include( plugin_dir_path( __FILE__ ) . 'vidyen-user-market-menu'); //Order 660

/*** AJAX ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/ajax/vyps_mmo_bal_ajax.php');
include( plugin_dir_path( __FILE__ ) . 'includes/functions/ajax/vyps_mmo_exchange_ajax.php');
