<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Adding menus. for the wm
add_action('admin_menu', 'vidyen_wm_menu', 286);

//Sub menu. Adding it to the VYPS system.


function vidyen_wm_menu()
{
	$parent_menu_slug = 'vyps_points';
	$page_title = 'Crypto Webminer';
	$menu_title = "Crypto Webminer";
	$capability = 'manage_options';
	$menu_slug = 'vidyen_wm';
	$function = 'vidyen_wm_sub_menu_page';
	add_submenu_page($parent_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function);

}

//The actual menu
function vidyen_wm_sub_menu_page()
{
	global $wpdb;

	$message = ''; //Init the menu message before the $_POST check.

	if (isset($_POST['crypto_wallet']))
	{
		//As the post is the only thing that edits data, I suppose this is the best place to the noce
		$vyps_nonce_check = $_POST['vypsnoncepost'];
		if ( ! wp_verify_nonce( $vyps_nonce_check, 'vidyen-vy-wm-nonce' ) )
    {
				// This nonce is not valid.
				die( 'Security check' );
		}

		$vy_wm_parsed_array = vidyen_vy_wm_settings();
		$index = 1; //Lazy coding but easier to copy and paste stuff.

		//Text for button
		if (isset($_POST['button_text']))
		{
			$button_text = sanitize_text_field($_POST['button_text']);
		}
		else
		{
			$button_text  = $vy_wm_parsed_array[$index]['button_text']; //make this the sql call to pull it
		}

		//The disclaimer text
		if (isset($_POST['disclaimer_text']))
		{
			$disclaimer_text = sanitize_textarea_field($_POST['disclaimer_text']);
		}
		else
		{
			$disclaimer_text  = $vy_wm_parsed_array[$index]['disclaimer_text'];
		}

		//The EULA text. The text below the button if they claim to have read it.
		if (isset($_POST['eula_text']))
		{
			$eula_text = sanitize_textarea_field($_POST['eula_text']);
		}
		else
		{
			$eula_text  = $vy_wm_parsed_array[$index]['eula_text'];
		}

		//The EULA text. The text below the button if they claim to have read it.
		if (isset($_POST['login_text']))
		{
			$login_text = sanitize_textarea_field($_POST['login_text']);
		}
		else
		{
			$login_text  = $vy_wm_parsed_array[$index]['login_text'];
		}

		//sanitize_url
		//The login url (optional) to have button redirect to login.
		if (isset($_POST['login_url']))
		{
			$login_url = esc_url_raw($_POST['login_url']);
		}
		else
		{
			$login_url  = $vy_wm_parsed_array[$index]['login_url'];
		}

		//The current WPM server (NOTE Not the pool but the proxy)
		if (isset($_POST['current_wmp']))
		{
			$current_wmp = sanitize_text_field($_POST['current_wmp']);
		}
		else
		{
			$current_wmp  = $vy_wm_parsed_array[$index]['current_wmp'];
		}

		//The current pool
		if (isset($_POST['current_pool']))
		{
			$current_pool = sanitize_text_field($_POST['current_pool']);
		}
		else
		{
			$current_pool  = $vy_wm_parsed_array[$index]['current_pool'];
		}

		//Going to slugify to worker readable
		if (isset($_POST['site_name']))
		{
			$site_name = sanitize_title($_POST['site_name']);
		}
		else
		{
			$site_name  = $vy_wm_parsed_array[$index]['site_name'];
		}

		//The crypto wallet. This field should have been required but *shrugs*
		if (isset($_POST['crypto_wallet']))
		{
			$crypto_wallet = sanitize_text_field($_POST['crypto_wallet']);
		}
		else
		{
			$crypto_wallet  = $vy_wm_parsed_array[$index]['crypto_wallet'];
		}

		//Hash per point. Almost forgot about this
		if (isset($_POST['hash_per_point']))
		{
			$hash_per_point = floatval($_POST['hash_per_point']);

			//If you are going to not give rewards then why bother?
			if ($hash_per_point < 1)
			{
				$hash_per_point = 1;
			}
		}
		else
		{
			$hash_per_point  = $vy_wm_parsed_array[$index]['hash_per_point'];
		}

		//This reward type.
		if (isset($_POST['point_id']))
		{
			$point_id = intval($_POST['point_id']);

			//There are neither point ids at 0 or negative
			if ($point_id < 1)
			{
				$point_id = 1;
			}
		}
		else
		{
			$point_id  = $vy_wm_parsed_array[$index]['point_id'];
		}

		//Graphics selection. In theory this will all be set by defaultor something went wrong.
		//NOTE: I feel this is rather clunky and inefficient, but users won't be hitting the menu
		//And admins will set this a few times and then leave it.
		if (isset($_POST['graphic_girl']))
		{
			//Build the string. Each of these should be a 1 or 0.
			$girl_graphic_selection = 'girl='.intval($_POST['graphic_girl']);
		}
		else
		{
			$girl_graphic_selection  = 'girl=0';
		}

		if (isset($_POST['graphic_guy']))
		{
			//Build the string. Each of these should be a 1 or 0.
			$guy_graphic_selection = '&guy='.intval($_POST['graphic_guy']);
		}
		else
		{
			$guy_graphic_selection  = '&guy=0';
		}

		if (isset($_POST['graphic_cyber']))
		{
			//Build the string. Each of these should be a 1 or 0.
			$cyber_graphic_selection = '&cyber='.intval($_POST['graphic_cyber']);
		}
		else
		{
			$cyber_graphic_selection  = '&cyber=0';
		}

		if (isset($_POST['graphic_undead']))
		{
			//Build the string. Each of these should be a 1 or 0.
			$undead_graphic_selection = '&undead='.intval($_POST['graphic_undead']);
		}
		else
		{
			$undead_graphic_selection  = '&undead=0';
		}

		if (isset($_POST['graphic_peasant']))
		{
			//Build the string. Each of these should be a 1 or 0.
			$peasant_graphic_selection = '&peasant='.intval($_POST['graphic_peasant']);
		}
		else
		{
			$peasant_graphic_selection  = '&peasant=0';
		}

		if (isset($_POST['graphic_youtube']))
		{
			//Build the string. Each of these should be a 1 or 0.
			$youtube_graphic_selection = '&youtube='.intval($_POST['graphic_youtube']);
		}
		else
		{
			$youtube_graphic_selection  = '&youtube=0';
		}

		//Time to build it and shove it into the table.
		$graphic_selection = $girl_graphic_selection;
		$graphic_selection .= $guy_graphic_selection;
		$graphic_selection .= $cyber_graphic_selection;
		$graphic_selection .= $undead_graphic_selection;
		$graphic_selection .= $peasant_graphic_selection;
		$graphic_selection .= $youtube_graphic_selection;


		//But is it possible to have the WM off while the GK is active.
		if (isset($_POST['wm_pro_active']))
		{
			$wm_pro_active = intval($_POST['wm_pro_active']); //should be 1 or 0
		}
		else
		{
			$wm_pro_active  = 0;
		}

		//I found this easy to do direct to woocommerce.
		if (isset($_POST['wm_woo_active']))
		{
			$wm_woo_active = intval($_POST['wm_woo_active']); //should be 1 or 0
		}
		else
		{
			$wm_woo_active  = 0;
		}

		//Low
		//The desired amount of threads
		if (isset($_POST['wm_threads_low']))
		{
			$wm_threads_low = intval($_POST['wm_threads_low']);
		}
		else
		{
			$wm_threads_low  = $vy_wm_parsed_array[$index]['wm_threads_low'];
		}

		//The desired amount of throttle
		if (isset($_POST['wm_cpu_low']))
		{
			$wm_cpu_low = intval($_POST['wm_cpu_low']);
		}
		else
		{
			$wm_cpu_low  = $vy_wm_parsed_array[$index]['wm_cpu_low'];
		}

		//medium
		//The desired amount of threads
		if (isset($_POST['wm_threads_medium']))
		{
		  $wm_threads_medium = intval($_POST['wm_threads_medium']);
		}
		else
		{
		  $wm_threads_medium  = $vy_wm_parsed_array[$index]['wm_threads_medium'];
		}

		//The desired amount of throttle
		if (isset($_POST['wm_cpu_medium']))
		{
		  $wm_cpu_medium = intval($_POST['wm_cpu_medium']);
		}
		else
		{
		  $wm_cpu_medium  = $vy_wm_parsed_array[$index]['wm_cpu_medium'];
		}

		//high
		//The desired amount of threads
		if (isset($_POST['wm_threads_high']))
		{
		  $wm_threads_high = intval($_POST['wm_threads_high']);
		}
		else
		{
		  $wm_threads_high  = $vy_wm_parsed_array[$index]['wm_threads_high'];
		}

		//The desired amount of throttle
		if (isset($_POST['wm_cpu_high']))
		{
		  $wm_cpu_high = intval($_POST['wm_cpu_high']);
		}
		else
		{
		  $wm_cpu_high  = $vy_wm_parsed_array[$index]['wm_cpu_high'];
		}

		//sanitize_url
		//The discord_webhook (optional) neet feature I've been playing with
		if (isset($_POST['discord_webhook']))
		{
			$discord_webhook = esc_url_raw($_POST['discord_webhook']);
		}
		else
		{
			$discord_webhook  = $vy_wm_parsed_array[$index]['discord_webhook'];
		}

		//The discord text message so people can use the hook with custom message.
		if (isset($_POST['discord_text']))
		{
			$discord_text = sanitize_textarea_field($_POST['discord_text']);
		}
		else
		{
			$discord_text  = $vy_wm_parsed_array[$index]['discord_text'];
		}

		if (isset($_POST['youtube_url']))
		{
			$youtube_url = esc_url_raw($_POST['youtube_url']);
		}
		else
		{
			$youtube_url  = $vy_wm_parsed_array[$index]['youtube_url'];
		}

		if (isset($_POST['custom_wmp']))
		{
			$custom_wmp = sanitize_text_field($_POST['custom_wmp']);
		}
		else
		{
			$custom_wmp  = $vy_wm_parsed_array[$index]['custom_wmp'];
		}

    $table_name_vy_wm = $wpdb->prefix . 'vidyen_wm_settings';

		//Default data
	  $data = [
	      'button_text' => $button_text,
	      'disclaimer_text' => $disclaimer_text,
	      'eula_text' => $eula_text,
				'login_text' => $login_text,
				'login_url' => $login_url,
	      'current_wmp' => $current_wmp,
	      'current_pool' => $current_pool,
	      'site_name' => $site_name,
	      'crypto_wallet' => $crypto_wallet,
				'hash_per_point' => $hash_per_point,
				'point_id' => $point_id,
				'graphic_selection' => $graphic_selection,
	      'wm_pro_active' => $wm_pro_active,
				'wm_woo_active' => $wm_woo_active,
				'discord_webhook' => $discord_webhook,
				'discord_text' => $discord_text,
				'youtube_url' => $youtube_url,
				'custom_wmp' => $custom_wmp,
				'wm_threads_low' => $wm_threads_low,
				'wm_cpu_low' => $wm_cpu_low,
				'wm_threads_medium' => $wm_threads_medium,
				'wm_cpu_medium' => $wm_cpu_medium,
				'wm_threads_high' => $wm_threads_high,
				'wm_cpu_high' => $wm_cpu_high,
	  ];

			$wpdb->update($table_name_vy_wm, $data, ['id' => 1]);
	    //$data_id = $wpdb->update($table_name_vy_wm , $data);

	    //I forget thow this works
	    $message = "Settings Saved";
	}

	$vy_wm_parsed_array = vidyen_vy_wm_settings();
	$index = 1; //Lazy coding but easier to copy and paste stuff.
  //Repulls from SQL
	$button_text = $vy_wm_parsed_array[$index]['button_text'];
	$disclaimer_text = $vy_wm_parsed_array[$index]['disclaimer_text'];
	$eula_text = $vy_wm_parsed_array[$index]['eula_text'];
	$login_text = $vy_wm_parsed_array[$index]['login_text'];
	$login_url = $vy_wm_parsed_array[$index]['login_url'];
	$current_wmp = $vy_wm_parsed_array[$index]['current_wmp'];
	$current_pool = $vy_wm_parsed_array[$index]['current_pool'];
	$site_name = $vy_wm_parsed_array[$index]['site_name'];
	$crypto_wallet = $vy_wm_parsed_array[$index]['crypto_wallet'];
	$hash_per_point = $vy_wm_parsed_array[$index]['hash_per_point'];
	$point_id = 	$vy_wm_parsed_array[$index]['point_id'];
	$graphic_selection = $vy_wm_parsed_array[$index]['graphic_selection'];
	$wm_pro_active = $vy_wm_parsed_array[$index]['wm_pro_active'];
	$wm_woo_active = $vy_wm_parsed_array[$index]['wm_woo_active'];
	$wm_threads = $vy_wm_parsed_array[$index]['wm_threads'];
	$wm_cpu = $vy_wm_parsed_array[$index]['wm_cpu'];
	$discord_webhook = $vy_wm_parsed_array[$index]['discord_webhook'];
	$discord_text = $vy_wm_parsed_array[$index]['discord_text'];
	$youtube_url = $vy_wm_parsed_array[$index]['youtube_url'];
  $custom_wmp = $vy_wm_parsed_array[$index]['custom_wmp'];
	$wm_threads_low = $vy_wm_parsed_array[$index]['wm_threads_low'];
	$wm_cpu_low = $vy_wm_parsed_array[$index]['wm_cpu_low'];
	$wm_threads_medium = $vy_wm_parsed_array[$index]['wm_threads_medium'];
	$wm_cpu_medium = $vy_wm_parsed_array[$index]['wm_cpu_medium'];
	$wm_threads_high = $vy_wm_parsed_array[$index]['wm_threads_high'];
	$wm_cpu_high = $vy_wm_parsed_array[$index]['wm_cpu_high'];


	//It dawned on me that these need to go only oce after the SQL parse has been redone.
	if ($wm_pro_active == 1)
	{
		$wm_pro_checked = 'checked';
		$max_threads = 20; //Yes yes, you an go 20 max threads, but only if user can handle.
		$woo_mode_disabled = '';
		$discord_webhook_disabled = '';
		$discord_text_disabled = '';
		$youtube_url_disabled = '';
		$custom_wmp_url_disabled = '';

		//It dawned on me that these need to go only oce after the SQL parse has been redone.
		if ($wm_woo_active == 1)
		{
			$wm_woo_checked = 'checked';
		}
		else
		{
			$wm_woo_checked = '';
		}
	}
	else
	{
		$wm_pro_checked = '';
		$max_threads = 8;
		$wm_woo_checked = 'disabled';
		$discord_webhook_disabled = 'disabled';
		$discord_text_disabled = 'disabled';
		$youtube_url_disabled = 'disabled';
		$custom_wmp_url_disabled = 'disabled';
	}

	$vy_algo_selected = '';
	$vy_pico_selected = '';
	$mo_algo_selected = '';

	//It dawned on me that these need to go only oce after the SQL parse has been redone.
	if ($current_wmp == 'savona.vy256.com:8183')
	{
		$vy_algo_selected = 'selected';
	}
	elseif ($current_wmp == 'igori.vy256.com:8256')
	{
		$vy_pico_selected = 'selected';
	}
	elseif ($current_wmp == 'webminer.moneroocean.stream:443')
	{
		$mo_algo_selected = 'selected';
	}

	//Parse graphics selection. I use my own string for SQL
	wp_parse_str($graphic_selection, $graphics_selection_arary);

	//Some DEBUG for above
	//echo 'Girl Result:<br>' . intval($graphics_selection_arary['girl']);

	//Inite the variables.
	$image_random_selected = '';
	$image_girl_selected = '';
	$image_guy_selected = '';
	$image_cyber_selected = '';
	$image_undead_selected = '';
	$image_peasant_selected = '';
	$image_youtube_selected = '';

	//NOTE: These have to checked each and not an elseif since they all could be true
	//We actually need to check each one. May not be the most efficient.
	if(intval($graphics_selection_arary['girl'])==1)
	{
		$image_girl_selected = 'checked';
	}

	//We actually need to check each one. May not be the most efficient.
	if(intval($graphics_selection_arary['guy'])==1)
	{
		$image_guy_selected = 'checked';
	}

	//We actually need to check each one. May not be the most efficient.
	if(intval($graphics_selection_arary['cyber'])==1)
	{
		$image_cyber_selected = 'checked';
	}

	//We actually need to check each one. May not be the most efficient.
	if(intval($graphics_selection_arary['undead'])==1)
	{
		$image_undead_selected = 'checked';
	}

	//We actually need to check each one. May not be the most efficient.
	if(intval($graphics_selection_arary['peasant'])==1)
	{
		$image_peasant_selected = 'checked';
	}

	//We actually need to check each one. May not be the most efficient.
	if(intval($graphics_selection_arary['youtube'])==1)
	{
		$image_youtube_selected = 'checked';
	}


	//GRAPHICS
	$VYPS_worker_url = plugins_url( 'images/stat_vyworker_001.gif', dirname(__FILE__) );
	$VYPS_worker_img = '<div><img src="'.$VYPS_worker_url.'" style="height: 64px;"></div>';

	$image_url_folder = plugins_url( 'images/', dirname(__FILE__) );

	$image_url_girl = '<div><img src="'.$image_url_folder.'vyworker_001.gif'.'" style="height: 64px;"></div>';
	$image_url_guy = '<div><img src="'.$image_url_folder.'vyworker_002.gif'.'" style="height: 64px;"></div>';
	$image_url_cyber = '<div><img src="'.$image_url_folder.'vyworker_003.gif'.'" style="height: 64px;"></div>';
	$image_url_undead = '<div><img src="'.$image_url_folder.'vyworker_004.gif'.'" style="height: 64px;"></div>';
	$image_url_peasant = '<div><img src="'.$image_url_folder.'vyworker_005.gif'.'" style="height: 64px;"></div>';
	$image_url_youtube = '<div><img src="'.$image_url_folder.'youtube_option.png'.'" style="height: 64px;"></div>';

	//It's possible we don't use the VYPS logo since no points.
  //$vyps_logo_url = plugins_url( 'includes/images/logo.png', __FILE__ );
	//$vidyen_vy_wm_logo_url = plugins_url( 'includes/images/vyvp-logo.png', __FILE__ );

	//Adding a nonce to the post
	$vyps_nonce_check = wp_create_nonce( 'vidyen-vy-wm-nonce' );

	//Static text for the base plugin
	$vidyen_wm_menu_html_ouput ='
	'.$VYPS_worker_img.'
	<h1>VidYen Crypto Webminer</h1>
	<p>Settings:</p>
	<table width=100%>
		<form method="post">
			<tr>
				<td valign="top"><b>Button Text:</b></td>
				<td valign="top"><input type="text" name="button_text" id="button_text" value="'.$button_text.'" size="128" required="true">
				<input type="hidden" name="vypsnoncepost" id="vypsnoncepost" value="'.$vyps_nonce_check.'"/></td>
			</tr>
			<tr>
				<td valign="top"><b>Disclaimer Text Above The Button:</b><br>HTML mark up<br><i>[img]image url[/img]<br>[b]bold[/b]<br>[br] for line breaks</i></td>
				<td valign="top"><textarea name="disclaimer_text" id="disclaimer_text" rows="6" cols="130" required="true">'.$disclaimer_text.'</textarea></td>
			</tr>
			<tr>
				<td valign="top"><b>EULA Text Below The Button:</b><br>HTML mark up<br><i>[img]image url[/img]<br>[b]bold[/b]<br>[br] for line breaks</i></td>
				<td valign="top"><textarea name="eula_text" id="eula_text" rows="6" cols="130">'.$eula_text.'</textarea> <i>(Optional)</i></td>
			</tr>
			<tr>
				<td valign="top"><b>Login Text:</b><br>Text that shows up if user not logged in.<br><i>[img]image url[/img]<br>[b]bold[/b]<br>[br] for line breaks</i></td>
				<td valign="top"><textarea name="login_text" id="login_text" rows="6" cols="130">'.$login_text.'</textarea></td>
			</tr>
			<tr>
				<td valign="top"><b>Login URL:</b><br><i>Button Directs This Login Page if user is not logged on.</i></td>
				<td valign="top"><input type="text" name="login_url" id="login_url" value="'.$login_url.'" size="128" > <i>(Optional)</i></a></td>
			</tr>
			<tr>
				<td valign="top"><b>Current Web Mining Pool Proxy Server:</b></td>
				<td valign="top">
					<select name="current_wmp" id="current_wmp">
						<option value="igori.vy256.com:8256" '.$vy_pico_selected.'>VidYen CN-PICO Only - Stable Rate and Most Mobile Friendly</option>
						<option value="savona.vy256.com:8183" '.$vy_algo_selected.'>VidYen Algo Switcher - Picks Most Profitable Algo</option>
						<option value="webminer.moneroocean.stream:443" '.$mo_algo_selected.'>MoneroOcean Algo Switcher - Picks Most Profitable Algo</option>
					</select>
					<a href="https://moneroocean.stream/#/dashboard" target="_blank"><i>Go here to see results of of mining on pool.</i></a>
				</td>
			</tr>
			<tr>
				<td valign="top"><b>Site Name:</b><br><i>Change this to a unique name if you run more than one site.</i></td>
				<td valign="top"><input type="text" name="site_name" id="site_name" value="'.$site_name.'" size="128" maxlength="64" required="true"></td>
			</tr>
			<tr>
				<td valign="top"><b>Your XMR Based Crypto Wallet:</b></td>
				<td valign="top"><input type="text" name="crypto_wallet" id="crypto_wallet" value="'.$crypto_wallet.'" size="128" minlength="90" required="true"> <a href="https://mymonero.com/" target="_blank"><i>Need a wallet? Go here.</i></a></td>
			</tr>
			<tr>
				<td valign="top"><b>Reward Point Type</b><br><i>This is the Point ID found under the Point List.</i></td>
				<td valign="top"><input type="number" name="point_id" id="point_id" value="'.$point_id.'" size="18" min="1" step="1" required="true"> <i><b>NOTE:</b> <a href="'.site_url() . '/wp-admin/admin.php?page=vyps_point_list">You need to create at least one reward point here.</a> </i></td>
			</tr>
			<tr>
				<td valign="top"><b>Hash Per Point</b><br><i>Hashes mined per reward point.</i></td>
				<td valign="top"><input type="number" name="hash_per_point" id="hash_per_point" value="'.$hash_per_point.'" size="18" min="1" step="1" required="true"><br><i><b>NOTE:</b> If WooCommerce mode, this round to smallest unit of your decimal places setting.<br>0 decimal places = $1 <br>2 decimal places = $0.01<br>8 decimal places = 0.00000001<br>This is set in WooCommerce Settings</i></td>
			</tr>
			<tr>
				<td valign="top"><b>Power Button Settings:</b><br><i><b>Note:</b><br>Thread counts above 8 are only possible on Pro Mode</i></td>
				<td valign="top">
					<table>
						<tr>
							<td><b>Low Power</b></td>
							<td>Threads</td>
							<td valign="top"><input type="number" name="wm_threads_low" id="wm_threads_low" value="'.$wm_threads_low.'" size="18" min="1" max="'.$max_threads.'" step="1" required="true"></td>
							<td>CPU</td>
							<td valign="top"><input type="number" name="wm_cpu_low" id="wm_cpu_low" value="'.$wm_cpu_low.'" size="18" min="1" max="100" step="1" required="true"></td>
						</tr>
						<tr>
						  <td><b>Medium Power</b></td>
						  <td>Threads</td>
						  <td valign="top"><input type="number" name="wm_threads_medium" id="wm_threads_medium" value="'.$wm_threads_medium.'" size="18" min="1" max="'.$max_threads.'" step="1" required="true"></td>
						  <td>CPU</td>
						  <td valign="top"><input type="number" name="wm_cpu_medium" id="wm_cpu_medium" value="'.$wm_cpu_medium.'" size="18" min="1" max="100" step="1" required="true"></td>
						</tr>
						<tr>
						  <td><b>High Power</b></td>
						  <td>Threads</td>
						  <td valign="top"><input type="number" name="wm_threads_high" id="wm_threads_high" value="'.$wm_threads_high.'" size="18" min="1" max="'.$max_threads.'" step="1" required="true"></td>
						  <td>CPU</td>
						  <td valign="top"><input type="number" name="wm_cpu_high" id="wm_cpu_high" value="'.$wm_cpu_high.'" size="18" min="1" max="100" step="1" required="true"></td>
						</tr>
					</table>
			</tr>
			<tr>
				<td valign="top"><b>Miner Graphics:</b><br><i>Check Or Uncheck Included Graphics you wish to use.<br>Checking more than one will pick one selected at random.<br>Unchecking all will leave use no animation graphic.</i></td>
				<td valign="top">
					<table>
						<tr>
							<td><input type="checkbox" name="graphic_girl" id="graphic_girl" value="1" '.$image_girl_selected.'>'.$image_url_girl.'</td>
							<td><input type="checkbox" name="graphic_guy" id="graphic_guy" value="1" '.$image_guy_selected.'>'.$image_url_guy.'</td>
							<td><input type="checkbox" name="graphic_cyber" id="graphic_cyber" value="1" '.$image_cyber_selected.'>'.$image_url_cyber.'</td>
							<td><input type="checkbox" name="graphic_undead" id="graphic_undead" value="1" '.$image_undead_selected.'>'.$image_url_undead.'</td>
							<td><input type="checkbox" name="graphic_peasant" id="graphic_peasant" value="1" '.$image_peasant_selected.'>'.$image_url_peasant.'</td>
							<td><input type="checkbox" name="graphic_youtube" id="graphic_youtube" value="1" '.$image_youtube_selected.'>'.$image_url_youtube.'</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td valign="top"><b>Pro Mode Features Below</b></td>
				<td valign="top"><i>Enable Pro Mode and Save to Activate</i></td>
			</tr>
			<tr>
				<td valign="top"><b>Pro Mode:</b><br><i>Disables branding and allows max of 20 threads.</i></td>
				<td valign="top"><input type="checkbox" name="wm_pro_active" id="wm_pro_active" value="1" '.$wm_pro_checked.'>Activate Pro Mode. <b>NOTE:</b> For every 10 minutes an end user mines, 15 seconds will be given as a fee to VidYen for development funding!</td>
			</tr>
			<tr>
				<td valign="top"><b>WooCommerce Mode:</b><br><i>Credits Go Straight To WooCommerce Credit<br><b>Note:</b> It is highly recommended to turn off email notifications<br>WooCommerce > Settings > Emails > New Wallet Transaction</i></td>
				<td valign="top"><input type="checkbox" name="wm_woo_active" id="wm_woo_active" value="1" '.$wm_woo_checked.'>Activate WooCommerce Mode. <b>NOTE:</b> Requires <a href="https://wordpress.org/plugins/woo-wallet/" target="_blank">TeraWallet</a>, <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a>, and Pro Mode</td>
			</tr>
			<tr>
				<td valign="top"><b>Discord Webhook:</b><br><i>Only Available in Pro Mode</i></td>
				<td valign="top"><input type="text" name="discord_webhook" id="discord_webhook" value="'.$discord_webhook.'" size="128" '.$discord_webhook_disabled.'> <a href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks" target="_blank"><i>Intro to Discord Webhooks</i></a></td>
			</tr>
			<tr>
				<td valign="top"><b>Discord Message:</b><br>Discord Markup<br><i>[user]<br>[amount]</i></td>
				<td valign="top"><textarea name="discord_text" id="discord_text" rows="3" cols="130" required="true" '.$discord_text_disabled.'>'.$discord_text.'</textarea></td>
			</tr>
			<tr>
				<td valign="top"><b>YouTube Graphic URL:</b><br><i>Uses Custom Video For The Graphic. Paste YouTube share link.</i></td>
				<td valign="top"><input type="text" name="youtube_url" id="youtube_url" value="'.$youtube_url.'" size="128" '.$youtube_url_disabled.'>
			</tr>
			<tr>
				<td valign="top"><b>Custom WMP:</b><br><i>Advanced! Uses custom Web Miner Pool proxy server.<br>Format:<br>servername:port<br>ie: webminer.moneroocean.stream:443<br>Will override the Proxy server settings above regardless of what set to.</i></td>
				<td valign="top"><input type="text" name="custom_wmp" id="custom_wmp" value="'.$custom_wmp.'" size="128" '.$custom_wmp_url_disabled.'>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			<tr>
			<tr>
				<td valign="top"><input type="submit" value="Save Settings" class="button button-primary"></td>
				<td valign="top"></td>
			</tr>
		</form>
	</table>
	<h2>Shortcode To Install On Page</h2>
	<p><input style="padding: 12px 20px; margin: 8px 0; box-sizing: border-box;" type=text" value="[vidyen-wm]" id="shortcode_output" width="21" readonly></p>
	<button onclick="copy_shortcode()" class="button button-primary">Copy Shortcode To Clipboard</button>
	<p>Copy and paste onto page where you want Webminer to go.</p>
	<script>
		//Thread Slider
		var thread_slider = document.getElementById("wm_threads_low");
		var thread_output = document.getElementById("thread_count_low");
		thread_output.innerHTML = thread_slider.value;

		thread_slider.oninput = function()
		{
		  thread_output.innerHTML = this.value;
		}

		//CPU Slider
		var wm_cpu_slider = document.getElementById("wm_cpu_low");
		var cpu_output = document.getElementById("cpu_use_low");
		cpu_output.innerHTML = wm_cpu_slider.value;

		wm_cpu_slider.oninput = function()
		{
			cpu_output.innerHTML = this.value;
		}

		//Copy shortcode link.
		function copy_shortcode()
		{
		  var copyText = document.getElementById("shortcode_output");
		  copyText.select();
		  document.execCommand("copy");
		  alert("Copied Shortcode: " + copyText.value);
		}
	</script>
	<br>
	<h2>Disclaimer</h2>
	<p><b>NOTE: For every 10 minutes an end user mines, 15 seconds will be given as a fee to VidYen for development funding!</b></p>
	<p>If you do not like it, it would do you good to learn how to code or go back to just giving away your own efforts for free.</p>
	<p>You are responsible for creating your own disclaimer that legally handles your current situation (consent to mining, cookies, and/or above 18 years or older)</p>
	<p>Disclaimer cookies last for 24 hours. This is intentional to remind users what they are doing.</p>
	<p>This monetization is quite advanced compared to receiving checks from Adsense, but as advertising revenue dwindles for independant site ownswers, it is recommended you educate yourself on how to use Monero and Monero Alt coins.</p>
	<p>Using this plugin will connect you to 3rd parties whose privacy policies are on their respective webpages.</p>
	<br>
	<h2>Instructions:</h2>
	<p>Fill out the fields where asked using your own Monero wallet to whichever pool of your choice. By default, it is set to the VidYen WMP and MoneroOcean pools as it tends to be AV and Adblock friendly as possible.</p>
	<h2>Video Tutorials</h2>
	<p>How to create a Monero Wallet</p>
	<iframe width="560" height="315" src="https://www.youtube.com/embed/x7yq-5PWWPo" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	<p>How a Monero Pool Works</p>
	<iframe width="560" height="315" src="https://www.youtube.com/embed/_HS4HQvqOUY" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	<h2>Spport</h2>
	<p>Feel free to open a post on the WordPress forums or reach out to us on the <a href="https://discord.gg/6svN5sS" target="_blank">VidYen Discord</a></p>
	<p>It is my personal intention to make WebMining the default way of monetization in the future. If you do have problems, I will be happy to look into regardless of the issue. -Felty</p>
	';

	//NOTE: I keep forgetting but wss://webminer.moneroocean.stream:443 is the webmining address for MO
	//Everything created with intelligence is only successful because of Chaos and no reason the creator

	//Going to pop up if there s is a a change. Making to look this nice.
	if(!empty($message))
	{

		echo '<div id="message" class="updated notice is-dismissible">
				<p><strong>'.$message.'</strong></p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
			</div>';
	}

  echo $vidyen_wm_menu_html_ouput;
}
