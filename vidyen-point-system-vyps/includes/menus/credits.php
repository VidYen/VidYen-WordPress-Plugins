<?php

//Simple credits file to update the list so it doesn't have to be updated every time

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Image URLS
//NOTE It took me a while to realize, I needed the dirname()
$vidyen_vidhash_logo_url = plugins_url( 'images/vidyen-vidhash.png', dirname(__FILE__) );
$vidyen_twitch_logo_url = plugins_url( 'images/vidyen-twitch.png', dirname(__FILE__) );
$vidyen_monero_share_logo_url = plugins_url( 'images/vidyen-monero-share.png', dirname(__FILE__) );
$vidyen_poker_game_logo_url = plugins_url( 'images/pokergame.png', dirname(__FILE__) );

//Plugin URLS
$vidyen_vidhash_plugin_url = 'https://wordpress.org/plugins/vidyen-vidhash/';
$vidyen_twitch_plugin_url = 'https://wordpress.org/plugins/vidyen-twitch-player/';
$vidyen_monero_share_plugin_url = 'https://wordpress.org/plugins/vidyen-monero-share/';
$vidyen_video_poker_plugin_url = 'https://www.vidyen.com/product/vidyen-video-poker/';

echo "
  <h1>Pro Version</h1>
  <p>There is a pro plugin valid until 2020 available for purchase on the <a href=\"https://www.vidyen.com/product/vyps-pro-install/\" target=\"_blank\">VidYen</a> store. It will remove the VYPS branding on the monetization pages.</p>
  <p>NOTE: You can use VYPS to earn credit toward its purchase.</p>
  <h2>PFor more development work, please check out  <a href=\"https://www.vidyen.com/vyps/\" target=\"_blank\">VidYen.com</a></h2>
  <p>Feel free to ping Felty on the <a href=\"https://discord.gg/6svN5sS\" target=\"_blank\">VidYen Discord</a> if you have any immediate questions.</p>
  <h2>Other VidYen Plugins</h2>
  <br>";

echo '
  <table>
    <tr>
      <td><a href="' . $vidyen_vidhash_plugin_url . '" target= "_blank"><img src="' . $vidyen_vidhash_logo_url . '" ></a></td>
      <td> </td>
      <td><a href="' . $vidyen_twitch_plugin_url . '" target= "_blank"><img src="' . $vidyen_twitch_logo_url . '" ></a></td>
      <td> </td>
      <td><a href="' . $vidyen_monero_share_plugin_url . '" target= "_blank"><img src="' . $vidyen_monero_share_logo_url . '" ></a></td>
    </tr>
    <tr>
      <td>VidYen YouTube Miner</td>
      <td> </td>
      <td>VidYen Twitch Miner</td>
      <td> </td>
      <td>VidYen Monero Share</td>
    </tr>
  </table>
  <h2>Available for Purchase</h2>
  <br><a href="' . $vidyen_video_poker_plugin_url . '" target= "_blank"><img src="' . $vidyen_poker_game_logo_url . '" ></a>
  ';

echo '
<h2>Export Data</h2>
<p>WordPress has instructons on how to export your tables <a href="https://codex.wordpress.org/Backing_Up_Your_Database" target="_blank">found here</a>.</p>
<p>The table for the user data for VYPS is called: <b>(db)_vyps_points_log</b></p>
<h2>Coming Soon:</h2>
<p>Combat Game</p>
<p>Strategy Game</p>
<h1>Warranty</h1>
<p>This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, version 2 of the License</p></p>
<p>This program is distributed with the intention to be useful but WITHOUT ANY WARRANTY, without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.</p>
<h2>This plugin uses the 3rd party services</h2>
<p>VidYen, LLC - To run websocket connections between your users client and the pool to distribute hash jobs. <a href="https://www.vidyen.com/privacy/" target="_blank">[Privacy Policy]</a></p>
<p>MoneroOcean - To provide mining stastics and handle the XMR payouts. <a href="https://moneroocean.stream/#/help/faq" target="_blank">[Privacy Policy]</a></p>
<p>Wannads - Offer Walls <a href="https://publishers.wannads.com/privacy" target="_blank">[Privacy Policy]</a></p>
<p>AdScend Media - Offer Walls <a href="https://adscendmedia.com/notices/privacy-policy" target="_blank">[Privacy Policy]</a></p>
<p>AdGate Media - Offer Walls <a href="https://adgatemedia.com/pp.php" target="_blank">[Privacy Policy]</a></p>
<p>Coinhive - To run websocket connections between your users client and put JS on your page. <a href="https://coinhive.com/info/privacy" target="_blank">[Privacy Policy]</a></p>
';

/* You know I'm not entirely sure if the ending of this PHP will cause problems.
*  According to WP standards it's not needed.
*/
