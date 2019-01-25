<?php

//Simple credits file to update the list so it doesn't have to be updated every time

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Image URLS
//NOTE It took me a while to realize, I needed the dirname()
$vidyen_vidhash_logo_url = plugins_url( 'images/vidyen-vidhash.png', dirname(__FILE__) );
$vidyen_twitch_logo_url = plugins_url( 'images/vidyen-twitch.png', dirname(__FILE__) );

//Plugin URLS
$vidyen_vidhash_plugin_url = 'https://wordpress.org/plugins/vidyen-vidhash/';
$vidyen_twitch_plugin_url = 'https://wordpress.org/plugins/vidyen-twitch-player/';

echo "
  <h1>Pro Version</h1>
  <p>There is a pro plugin valid until 2020 available for purchase on the <a href=\"https://www.vidyen.com/product/vyps-pro-install/\" target=\"_blank\">VidYen</a> store. It will remove the VYPS branding on the monetization pages.</p>
  <p>NOTE: You can use VYPS to earn credit toward its purchase.</p>
  <h2>PFor more development work, please check out  <a href=\"https://www.vidyen.com/vyps/\" target=\"_blank\">VidYen.com</a></h2>
  <p>Feel free to ping Felty on the <a href=\"https://discord.gg/6svN5sS\" target=\"_blank\">VidYen Discord</a> if you have any immediate questions.</p>
  <h2>Other VidYen Plugins</h2>";

echo '
  <table>
    <tr>
      <td><a href="' . $vidyen_vidhash_plugin_url . '" target= "_blank"><img src="' . $vidyen_vidhash_logo_url . '" ></a></td>
      <td> </td>
      <td><a href="' . $vidyen_twitch_plugin_url . '" target= "_blank"><img src="' . $vidyen_twitch_logo_url . '" ></a></td>
    </tr>
    <tr>
      <td>VidYen YouTube Miner</td>
      <td> </td>
      <td>VidYen Twitch Miner</td>
    </tr>
  </table>
  ';

echo "
<h2>Coming Soon:</h2>
<p>Combat Game</p>
<p>Strategy Game</p>
<h1>Warranty</h1>
<p>This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, version 2 of the License</p>
<p>This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.</p>
";

/* You know I'm not entirely sure if the ending of this PHP will cause problems.
*  According to WP standards it's not needed.
*/
