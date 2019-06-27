<?php
/*
Plugin Name:  VidYen VidHash
Plugin URI: https://wordpress.org/plugins/vidyen-vidhash/
Description:  Have users mine crypto currency while watching your embedded YouTube videos
Version:      1.5.5
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

register_activation_hook(__FILE__, 'vy_vidhash_install');

//Install the SQL tables for VY VidHash
function vy_vidhash_install()
{
	//Actually, I can't think of a need for the SQL just yet.
}

//Adding the menu function
add_action('admin_menu', 'vy_vidhash_menu');

function vy_vidhash_menu()
{
	//Only need to install the one menu to explain shortcode usage
  $parent_page_title = "VidYen VidHash";
  $parent_menu_title = 'VY VidHash';
  $capability = 'manage_options';
  $parent_menu_slug = 'vy_vidhash';
  $parent_function = 'vy_vidhash_parent_menu_page';
  add_menu_page($parent_page_title, $parent_menu_title, $capability, $parent_menu_slug, $parent_function);
}

//The actual page... I should throw this on its own include. Down the road maybe.
function vy_vidhash_parent_menu_page()
{
	//It's possible we don't use the VYPS logo since no points.
  $vy_logo_url = plugins_url( 'images/vy_logo.png', __FILE__ );
  $vy256_worker_url = plugins_url( 'images/vyworker_001.gif', __FILE__ );
  $vidhash_icon_url = plugins_url( 'images/icon-256x256.png', __FILE__ );

  //The HTML output.
	echo '<br><br><img src="' . $vidhash_icon_url . '" > ';

	//Static text for the base plugin
	echo
	"<h1>VidYen VidHash Embedded YouTube Miner</h1>
	<p>The plugin uses the VidYen Monero miner to mine while an embedded YouTube video is playing. It ties into the YouTube JS API and only mines while videos are being played.</p>
	<p>Does not use the VidYen Point System rewards, but at same time does not require you user to log in to mine for you. Just a cookie consent via an AJAX post.</p>
	<h2>Shortcode Instructions</h2>
	<p>Format: <b>[vy-vidhash wallet=(your XMR Wallet) url=(the code at the end of the video you want to embed)]</b></p>
  	<p>Optional for languages other than English:<b>[vy-vidhash disclaimer=\"Your message about cookies and resources\" button=\"the button text\"]</b></p>
  <h2>VidYen Point System Integration Rewards</h2>
  <p>To use with VYPS with VidHash, use the following format: <b>[vy-vidhash wallet=(wallet) url=(YouTube URL) vyps=true]</b></p>
  <p>Then with the VYPS plugin, use the following format: [vyps-256 wallet=(wallet) hash=(has per point) pid=(Point ID) site=(your site name) youtube=true]</b></p>
  <p>Working Example:</p>
  <p><b>[vy-vidhash wallet=8BpC2QJfjvoiXd8RZv3DhRWetG7ybGwD8eqG9MZoZyv7aHRhPzvrRF43UY1JbPdZHnEckPyR4dAoSSZazf5AY5SS9jrFAdb url=https://youtu.be/Uc70UBBBIVk vyps=true]</b></p>
  <p><b>[vyps-256 wallet=8BpC2QJfjvoiXd8RZv3DhRWetG7ybGwD8eqG9MZoZyv7aHRhPzvrRF43UY1JbPdZHnEckPyR4dAoSSZazf5AY5SS9jrFAdb hash=10000 pid=4 site=vidyenlive youtube=true]</b></p>
  <p>Note: You must use latest version of VYPS 2.2.0 for this to work and you do not need a consent button shortcode as it uses the VidHash method of cookie consent.</p>
  <h2>Payout</h2>
	<p>Again this uses Monero Ocean for the backup like the VidYen point system.</p>
	<p>To see your progress towards payout, vist the <a href=\"https://moneroocean.stream/#/dashboard\" target=\"_blank\">dashboard</a> and add your XMR wallet where it says Enter Payment Address at bottom of page. There you can see total hashes, current hash rate, and account option if you wish to change payout rate.</p>
	<p>Keep in mind, unlike Coinhive, you can use this in conjunction with GPU miners to the same pool.</p>
	<p>Since this is running on our servers and we expanded the code, VidYen, LLC is the one handling the support. Please go to our <a href=\"https://www.vidyen.com/contact/\" target=\"_blank\">contact page</a> or if you need assistance immediatly, join the <a href=\"https://discord.gg/6svN5sS\" target=\"_blank\">VidYen Discord</a> and PM Felty. (It will ping my phone, so do not abuse. -Felty)</p></p>
  <h2>Getting a Monero wallet</h2>
  <p>If you are completely new to Monero and need a wallet address, you can quickly get one at <a href=\"https://mymonero.com/\" target=\"_blank\">My Monero</a> or if you want a more technical or secure wallet visit <a href=\"https://ww.getmonero.org/\" target=\"_blank\">Get Monero</a> on how to create an enanched wallet.</p>
  <p>If you have an iPhone you can always use  <a href=\"https://cakewallet.io/\" target=\"_blank\">Cake Wallet</a as well.</p>
	";

	echo '<br><br><img src="' . $vy256_worker_url . '" > ';
}

/*** BEGIN SHORTCODE INCLUDES ***/
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-vidhash-shortcode.php'); //For now just the actual SC [vy-vidhash]
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes/vidyen-vidhash-get-shortcode.php'); //For now just the actual SC [vy-vidhash]

/*** BEGIN FUNCTION INCLUDES ***/
include( plugin_dir_path( __FILE__ ) . 'includes/functions/vidyen-vidhash-functions.php'); //Adds the ajax for cookie consent
