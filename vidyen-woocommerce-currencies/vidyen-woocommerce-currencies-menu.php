<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Adding menus.
add_action('admin_menu', 'vidyen_woocommerce_currencies_menu');

//Sub menu. Adding it to the VYPS system.

function vidyen_woocommerce_currencies_menu()
{
	$parent_page_title = "VidYen woocommerce_currencies";
	$parent_menu_title = 'VY woocommerce_currencies';
	$capability = 'manage_options';
	$parent_menu_slug = 'vidyen_woocommerce_currencies';
	$parent_function = 'vidyen_woocommerce_currencies_menu_page';
	$icon_url = plugin_dir_url( __FILE__ ) . 'includes/images/coin.png';
	add_menu_page($parent_page_title, $parent_menu_title, $capability, $parent_menu_slug, $parent_function, $icon_url);

}

//The actual menu
function vidyen_woocommerce_currencies_menu_page()
{
	global $wpdb;

	if (isset($_POST['currency_name']))
	{
		//As the post is the only thing that edits data, I suppose this is the best place to the noce
		$vyps_nonce_check = $_POST['vypsnoncepost'];
		if ( ! wp_verify_nonce( $vyps_nonce_check, 'vidyen-woocommerce_currencies-nonce' ) )
    {
				// This nonce is not valid.
				die( 'Security check' );
		}

		$woocommerce_currencies_parsed_array = vidyen_woocommerce_currencies_settings();
		$index = 1; //Lazy coding but easier to copy and paste stuff.

		//Text for currency_name
		if (isset($_POST['currency_name']))
		{
			$currency_name = sanitize_text_field($_POST['currency_name']);
		}
		else
		{
			$currency_name  = $woocommerce_currencies_parsed_array[$index]['currency_name']; //make this the sql call to pull it
		}

		//The disclaimer text
		if (isset($_POST['currency_symbol']))
		{
			$currency_symbol = sanitize_text_field($_POST['currency_symbol']);
		}
		else
		{
			$currency_symbol  = $woocommerce_currencies_parsed_array[$index]['currency_symbol'];
		}

    $table_name_woocommerce_currencies = $wpdb->prefix . 'vidyen_woocommerce_currencies';

		//Default data
	  $data = [
	      'currency_name' => $currency_name,
	      'currency_symbol' => $currency_symbol,
	  ];

			$wpdb->update($table_name_woocommerce_currencies, $data, ['id' => 1]);
	    //$data_id = $wpdb->update($table_name_woocommerce_currencies , $data);

	    //I forget thow this works
	    $message = "Added successfully.";
	}

	$woocommerce_currencies_parsed_array = vidyen_woocommerce_currencies_settings();
	$index = 1; //Lazy coding but easier to copy and paste stuff.
  //Repulls from SQL
	$currency_name  = $woocommerce_currencies_parsed_array[$index]['currency_name'];
	$currency_symbol  = $woocommerce_currencies_parsed_array[$index]['currency_symbol'];

	//It's possible we don't use the VYPS logo since no points.
  //$vyps_logo_url = plugins_url( 'includes/images/logo.png', __FILE__ );
	//$vidyen_woocommerce_currencies_logo_url = plugins_url( 'includes/images/vyvp-logo.png', __FILE__ );

	//Adding a nonce to the post
	$vyps_nonce_check = wp_create_nonce( 'vidyen-woocommerce_currencies-nonce' );

  $VYPS_worker_url = plugins_url( 'includes/images/stat_vyworker_001.gif',  __FILE__ );
	$VYPS_worker_img = '<div><img src="'.$VYPS_worker_url.'"></div>';
	//Static text for the base plugin
	$vidyen_woocommerce_currencies_menu_html_ouput ='
	<br>'.$VYPS_worker_img.'
	<h1>VidYen Woocommerce Currencies</h1>
	<p>Settings:</p>
	<table width=100%>
		<form method="post">
			<tr>
				<td><b>Currency Name:</b></td>
				<td><input type="text" name="currency_name" id="currency_name" value="'.$currency_name.'" size="128" required="true">
				<input type="hidden" name="vypsnoncepost" id="vypsnoncepost" value="'.$vyps_nonce_check.'"/></td>
			</tr>
			<tr>
				<td valign="top"><b>Currency Symbol</b></td>
				<td><input type="text" name="currency_symbol" id="currency_symbol" value="'.$currency_symbol.'" size="128" required="true">
				</tr>
			<tr>
				<td><input type="submit" value="Save Settings"></td>
				<td></td>
			</tr>
		</form>
	</table>
	';

  echo $vidyen_woocommerce_currencies_menu_html_ouput;
}
