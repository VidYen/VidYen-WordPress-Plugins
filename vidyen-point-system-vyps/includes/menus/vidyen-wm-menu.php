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
			$disclaimer_text = sanitize_text_field($_POST['disclaimer_text']);
		}
		else
		{
			$disclaimer_text  = $vy_wm_parsed_array[$index]['disclaimer_text'];
		}

		//The EULA text. The text below the button if they claim to have read it.
		if (isset($_POST['eula_text']))
		{
			$eula_text = sanitize_text_field($_POST['eula_text']);
		}
		else
		{
			$eula_text  = $vy_wm_parsed_array[$index]['eula_text'];
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

		//The current site name if supplied
		if (isset($_POST['site_name']))
		{
			$site_name = sanitize_text_field($_POST['site_name']);
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

		//But is it possible to have the WM off while the GK is active.
		if (isset($_POST['wm_pro_active']))
		{
			$wm_pro_active = intval($_POST['wm_pro_active']); //should be 1 or 0
		}
		else
		{
			$wm_pro_active  = 0;
		}

		//The desired amount of threads
		if (isset($_POST['wm_threads']))
		{
			$wm_threads = intval($_POST['wm_threads']);
		}
		else
		{
			$wm_threads  = $vy_wm_parsed_array[$index]['wm_threads'];
		}

		//So if the pro is not active and they set threads to greater than 6 it gets set to 6
		if ($wm_pro_active == 0 AND $wm_threads > 6 )
		{
			$wm_threads = 6;
		}

		//The desired amount of throttle
		if (isset($_POST['wm_cpu']))
		{
			$wm_cpu = intval($_POST['wm_cpu']);
		}
		else
		{
			$wm_cpu  = $vy_wm_parsed_array[$index]['wm_cpu'];
		}

    $table_name_vy_wm = $wpdb->prefix . 'vidyen_wm_settings';

		//Default data
	  $data = [
	      'button_text' => $button_text,
	      'disclaimer_text' => $disclaimer_text,
	      'eula_text' => $eula_text,
	      'current_wmp' => $current_wmp,
	      'current_pool' => $current_pool,
	      'site_name' => $site_name,
	      'crypto_wallet' => $crypto_wallet,
	      'wm_pro_active' => $wm_pro_active,
				'wm_threads' => $wm_threads,
				'wm_cpu' => $wm_cpu,
	  ];

			$wpdb->update($table_name_vy_wm, $data, ['id' => 1]);
	    //$data_id = $wpdb->update($table_name_vy_wm , $data);

	    //I forget thow this works
	    $message = "Added successfully.";
	}

	$vy_wm_parsed_array = vidyen_vy_wm_settings();
	$index = 1; //Lazy coding but easier to copy and paste stuff.
  //Repulls from SQL
	$button_text  = $vy_wm_parsed_array[$index]['button_text'];
	$disclaimer_text  = $vy_wm_parsed_array[$index]['disclaimer_text'];
	$eula_text  = $vy_wm_parsed_array[$index]['eula_text'];
	$current_wmp  = $vy_wm_parsed_array[$index]['current_wmp'];
	$current_pool  = $vy_wm_parsed_array[$index]['current_pool'];
	$site_name  = $vy_wm_parsed_array[$index]['site_name'];
	$crypto_wallet  = $vy_wm_parsed_array[$index]['crypto_wallet'];
	$wm_pro_active  = $vy_wm_parsed_array[$index]['wm_pro_active'];
	$wm_threads = $vy_wm_parsed_array[$index]['wm_threads'];
	$wm_cpu = $vy_wm_parsed_array[$index]['wm_cpu'];


	//It dawned on me that these need to go only oce after the SQL parse has been redone.
	if ($wm_pro_active == 1)
	{
		$wm_pro_checked = 'checked';
	}
	else
	{
		$wm_pro_checked = '';
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

	//It's possible we don't use the VYPS logo since no points.
  //$vyps_logo_url = plugins_url( 'includes/images/logo.png', __FILE__ );
	//$vidyen_vy_wm_logo_url = plugins_url( 'includes/images/vyvp-logo.png', __FILE__ );

	//Adding a nonce to the post
	$vyps_nonce_check = wp_create_nonce( 'vidyen-vy-wm-nonce' );
  $VYPS_worker_url = plugins_url( 'images/stat_vyworker_001.gif', dirname(__FILE__) );
	$VYPS_worker_img = '<div><img src="'.$VYPS_worker_url.'" style="height: 128px;"></div>';
	//Static text for the base plugin
	$vidyen_wm_menu_html_ouput ='
	'.$VYPS_worker_img.'
	<h1>VidYen Crypto Webminer</h1>
	<p>Settings:</p>
	<table width=100%>
		<form method="post">
			<tr>
					<td><b>Button Text:</b></td>
				<td><input type="text" name="button_text" id="button_text" value="'.$button_text.'" size="128" required="true">
				<input type="hidden" name="vypsnoncepost" id="vypsnoncepost" value="'.$vyps_nonce_check.'"/></td>
			</tr>
			<tr>
			<td valign="top"><b>Disclaimer Text Above The Button:</b><br>HTML mark up<br><i>[img]image url[/img]<br>[b]bold[/b]<br>[br] for line breaks</i></td>
			<td><textarea name="disclaimer_text" id="disclaimer_text" rows="10" cols="130" required="true">'.$disclaimer_text.'</textarea></td>
			</tr>
			<tr>
			<td valign="top"><b>EULA Text Below The Button:</b><br>HTML mark up<br><i>[img]image url[/img]<br>[b]bold[/b]<br>[br] for line breaks</i></td>
			<td><textarea name="eula_text" id="eula_text" rows="10" cols="130">'.$eula_text.'</textarea></td>
			</tr>
			<tr>
				<td><b>Current Web Mining Pool Proxy Server:</b></td>
				<td>
					<select name="current_wmp" id="current_wmp">
						<option value="savona.vy256.com:8183" '.$vy_algo_selected.'>VidYen Algo Switcher</option>
						<option value="igori.vy256.com:8256" '.$vy_pico_selected.'>VidYen CN-PICO Only</option>
						<option value="webminer.moneroocean.stream:443" '.$mo_algo_selected.'>MoneroOcean Algo Switcher</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><b>Site Name:</b></td>
				<td><input type="text" name="site_name" id="site_name" value="'.$site_name.'" size="128"></td>
			</tr>
			<tr>
				<td><b>Your XMR Based Crypto Wallet:</b></td>
				<td><input type="text" name="crypto_wallet" id="crypto_wallet" value="'.$crypto_wallet.'" size="128" minlength="90" required="true"></td>
			</tr>
			<tr>
				<td><b>Default Threads:</b></td>
				<td><input type="range" name="wm_threads" id="wm_threads" step="1" min="1" max="4" value="'.$wm_threads.'" size="128" required="true"></td>
			</tr>
			<tr>
				<td><b>Default CPU USE:</b></td>
				<td><input type="range" step="1" min="0" max="100" value="'.$wm_cpu.'" class="slider" name="wm_cpu" id="wm_cpu" size="128"></td>
			</tr>
			<tr>
				<td><b>Miner Graphic:</b></td>
				<td>
					<select name="current_wmp" id="current_wmp">
						<option value="random" '.$image_random_selected.'>Random</option>
						<option value="girlworker" '.$image_girl_selected.'>Girl Miner</option>
						<option value="guyworker" '.$image_guy_selected.'>Guy Miner</option>
						<option value="cyberworker" '.$image_cyber_selected.'>Cyber Miner</option>
						<option value="uneadworker" '.$image_undead_selected.'>Undead Miner</option>
						<option value="peasantworker" '.$image_peasant_selected.'>Peasant Miner</option>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top"><b>Pro Mode:</b><br><i>Disables branding and allows unlimited threads.</i></td>
				<td><input type="checkbox" name="wm_pro_active" id="wm_pro_active" value="1" '.$wm_pro_checked.'>Activate Pro Mode. <b>NOTE:</b> For every 10 minutes an end user mines, 15 seconds will be given as a fee to VidYen for development funding!</td>
			</tr>
			<tr>
				<td><input type="submit" value="Save Settings"></td>
				<td></td>
			</tr>
		</form>
	</table>
	<h2>Disclaimer</h2>
	<p><b>NOTE: For every 10 minutes an end user mines, 15 seconds will be given as a fee to VidYen for development funding!</b></p>
	<p>If you do not like it, it would do you good to learn how to code or go back to just giving away your own efforts for free.</p>
	<p>You are responsible for creating your own disclaimer that legally handles your current situation (consent to mining, cookies, and/or above 18 years or older)</p>
	<p>Disclaimer cookies last for 24 hours. This is intentional to remind users what they are doing.</p>
	<p>This monetization is quite advanced compared to receiving checks from Adsense, but as advertising revenue dwindles for independant site ownswers, it is recommended you educate yourself on how to use Monero and Monero Alt coins.</p>
	<p>Using this plugin will connect you to 3rd parties whose privacy policies are on their respective webpages.</p>
	<h2>Instructions:</h2>
	<p>Fill out the fields where asked using your own Monero wallet to whichever pool of your choice. By default, it is set to the VidYen WMP and MoneroOcean pools as it tends to be AV and Adblock friendly as possible.</p>
	<h2>Video Tutorial on how to create a Monero Wallet</h2>
	<iframe width="560" height="315" src="https://www.youtube.com/embed/x7yq-5PWWPo" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	<h2>Video Tutorial on how Monero Pools Work</h2>
	<iframe width="560" height="315" src="https://www.youtube.com/embed/_HS4HQvqOUY" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	<h2>Spport</h2>
	<p>Feel free to open a post on the WordPress forums or reach out to us on the <a href="https://discord.gg/6svN5sS" target="_blank">VidYen Discord</a></p>
	<p>It is my personal intention to make WebMining the default way of monetization in the future. If you do have problems, I will be happy to look into regardless of the issue. -Felty</p>
	';

	//NOTE: I keep forgetting but wss://webminer.moneroocean.stream:443 is the webmining address for MO
	//Everything created with intelligence is only successful because of Chaos and no reason the creator

  echo $vidyen_wm_menu_html_ouput;
}
