<?php
 /*
Plugin Name:  VidYen MMO WebMiner
Plugin URI:   https://wordpress.org/plugins/vidyen-point-system-vyps/
Description:  MMO Webminer meant for in game usage
Version:      0.1.1
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

/*** SHORTCODE INCLUDES IN BASE ***/
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-mmo-vy256.php'); //Point Log

/*** AJAX ***/
//include( plugin_dir_path( __FILE__ ) . 'includes/functions/ajax/vyps_ajaxurl.php'); //Forces ajax to be called regardless of installation
//include( plugin_dir_path( __FILE__ ) . 'includes/functions/ajax/vyps_mo_ajax.php'); //MO Pull ajax
