<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
** I have decided to move this main menu to folder in save top directory.
**  Technically its not includes, but its not that hard to find.
*/

//Adding menus.
add_action('admin_menu', 'vidyen_wc_user_exchange_sub_menu', 666 );

//Sub menu. Adding it to the VYPS system.

function vidyen_wc_user_exchange_sub_menu()
{
	$parent_menu_slug = 'vyps_points';
	$page_title = "VidYen User Exchange";
  $menu_title = 'User Exchange Menu';
	$capability = 'manage_options';
  $menu_slug = 'vyps_wc_user_exchange_page';
  $function = 'vidyen_wc_user_exchange_sub_menu_page';

  add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
}

//The actual menu
function vidyen_wc_user_exchange_sub_menu_page()
{

  //Save for later //<img src="' . $vidyen_wc_user_exchange_logo_url . '">

	//Static text for the base plugin
	$vyps_wc_user_exchange_menu_html_ouput ='<br><br>
	<h1>VidYen WC User Exchange Sub-Plugin</h1>
	<p>Exchange Rates</p>
	<table>
		<form method="post">
			<tr>
				<th>VidYen Point ID</th>
				<th>Input Amount</th>
        <th>WooWallet Icon</th>
				<th>WooWallet Amount</th>
				<th>API Key (To Reset Leave Blank)</th>
				<th>Submit</th>
			</tr>
			<tr>
				<td><input type="number" name="point_id" type="number" id="point_id" min="1" step="1" value="' . $point_id .  '" required="true">
				<input type="hidden" name="vypsnoncepost" id="vypsnoncepost" value="'. $vyps_nonce_check . '"/></td>
				<td><input type="number" name="point_amount" type="number" id="point_amount" min="1" max="1000000" step="1" value="' . $point_amount . '" required="true"></td>
        <td><input type="number" name="output_id" type="number" id="output_id" min="1" step="1" value="' . $output_id .  '" required="true"></td>
				<td><input type="number" name="output_amount" type="number" id="output_amount" min="0.0000001" max="1000000" step="0.0000001" value="' . $output_amount . '" required="true"></td>
				<td><input type="text" name="api_key"  id="api_key" value="' . $api_key . '"></td>
				<td><input type="submit" value="Submit"></td>
			</tr>
		</form>
	</table>
	<h2>API Key Copy and Paste</h2>
	<p>'.$api_key.'</p>
	<h2>Shortcode</h2>
	<p><b>[vidyen-user_exchange-bal]</b> for live balance.</p>
	<p><b>[vyps-user_exchange-pe]</b> for live point exchange.</p>
	<p><b>[vidyen-user_exchange-deduct point_id=2 apikey=(set here on in User Exchange menu)]</b> This is a postback page. If you do not remember how to do the post back page watch the Wannads tutorial video in full.</p>
	<p><b>[vidyen-user_exchange-credit point_id=2 apikey=(set here on in User Exchange menu)]</b> Same as above but does the credit when you want to talk currency off server and into site.</p>
	<p><b>[vidyen-user_exchange-api-bal mode=GET gui=TRUE point_id=7]</b> This is a postback page for external curls. If you do not remember how to do the post back page watch the Wannads tutorial video in full.</p>
	<p><b>[vidyen-user_exchange-register apikey=test]</b> This is for your registration curl. Only enter your apikey where test is written.</p>
	<p><b>[vidyen-loa-id]</b> This is for your LoA userid box. It shows each user their currently stored LoA userid and lets them clear it if its incorrect. If edit=TRUE you are able to edit this directly from the website.</p>
	<p>Simply put the shortcodes on a page and let it run with the point id from the VidYen point system.</p>
	<p>Point ID is the point ID from the VidYen System. Found in Manage Points section of VYPS</p>
	<p>NOTE: If you change this settings while a game is in play, they must close browser or tab and reload page as is server session based.</p>
	<p>Requires the <a href="https://wordpress.org/plugins/vidyen-point-system-vyps/" target="_blank">VidYen Point System</a></p>
	<br><br><a href="https://wordpress.org/plugins/vidyen-point-system-vyps/" target="_blank"><img src="' . $vyps_logo_url . '"></a>
	';

  echo $vyps_wc_user_exchange_menu_html_ouput;
}
