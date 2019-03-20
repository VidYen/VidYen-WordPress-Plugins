<?php
 /*
Plugin Name:  VYPS WooCommerce MMO Plugin
Plugin URI:   https://wordpress.org/plugins/vidyen-point-system-vyps/
Description:  Adds RPG like currencies to WooCommerce for VYPS
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

//NOTE: Literally just lets you know function exists.
function vyps_flag_pro_adgate()
{
	return 1;
}

/*** Includes ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_wc_mmo_currency.php'); //Custom Currencies to WooCommerce
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vyps-adgate-postback.php'); //AdGate post back
include( plugin_dir_path( __FILE__ ) . 'includes/functions/core/vyps_adgate_pro_func.php'); //Adgate Pro checking.

/*** Menu Includes ***/
include( plugin_dir_path( __FILE__ ) . 'includes/menus/adgate-menu.php'); //Order 450 (residual from the extraction for core VYPS)
