<?php
/*
Plugin Name:  VidYen User Market
Plugin URI:   https://wordpress.org/plugins/vidyen-point-system-vyps/
Description:  Allows users to transfer points betwen each other.
Version:      0.0.4
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

/*** Shortcodes ***/
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-user-market-exchange.php');


/*** Menu Includes ***/
include( plugin_dir_path( __FILE__ ) . 'vidyen-user-market-menu.php'); //Order 660

/*** AJAX ***/
