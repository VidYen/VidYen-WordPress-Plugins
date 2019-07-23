<?php

//Improved shortcode of public log.


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Function removed and moved to function folder.
//Functions are found in \includes\function\wm\

//Shortcode for the log.

add_shortcode( 'vidyen-wm', 'vidyen_wm_shortcode_func');

//This is the shortcode function placed in a proper place rather than lumped together
//Going to try to avoid recyling code, but you know how these thigns go.
function vidyen_wm_shortcode_func()
{
  //First things first. Let's pull the variables with a single SQL call
  $vy_wm_parsed_array = vidyen_vy_wm_settings();
  $index = 1; //Lazy coding but easier to copy and paste stuff.
  //Repulls from SQL
  $button_text = $vy_wm_parsed_array[$index]['button_text'];
  $disclaimer_text = $vy_wm_parsed_array[$index]['disclaimer_text'];
  $eula_text = $vy_wm_parsed_array[$index]['eula_text'];
  $current_wmp = $vy_wm_parsed_array[$index]['current_wmp'];
  $current_pool = $vy_wm_parsed_array[$index]['current_pool'];
  $site_name = $vy_wm_parsed_array[$index]['site_name'];
  $crypto_wallet = $vy_wm_parsed_array[$index]['crypto_wallet'];
  $graphic_selection = $vy_wm_parsed_array[$index]['graphic_selection'];
  $wm_pro_active = $vy_wm_parsed_array[$index]['wm_pro_active'];
  $wm_woo_active = $vy_wm_parsed_array[$index]['wm_woo_active'];
  $wm_threads = $vy_wm_parsed_array[$index]['wm_threads'];
  $wm_cpu = $vy_wm_parsed_array[$index]['wm_cpu'];
  $discord_webhook = $vy_wm_parsed_array[$index]['discord_webhook'];
  $discord_text = $vy_wm_parsed_array[$index]['discord_text'];
  $youtube_url = $vy_wm_parsed_array[$index]['youtube_url'];
  $login_text = $vy_wm_parsed_array[$index]['login_text'];
  $login_url = $vy_wm_parsed_array[$index]['login_url'];

  //Init:
  $top_output = '';
  $mid_output = '';
  $bottom_output = '';
  $table_align = 'left';

  //GRAPHICS
  $image_url_folder = plugins_url( 'images/', dirname(__FILE__) );
  $vidyen_login_worker_img = '<img src="'.$image_url_folder.'stat_vyworker_006.gif" style="height: 64px;">';

  if (!is_user_logged_in())
  {
    $table_align = 'center';
    $top_output = '<div>'.$login_text.'</div>';
    $mid_output = '<button id="startb" style="width:100%;" onclick="vidyen_login_redirect()">Login</button>
    <script>
      function vidyen_login_redirect()
      {
        location.replace("'.$login_url.'")
      }
    </script>';
    $bottom_output = '<div style="text-align: center; display: inline-block;"><center>'.$vidyen_login_worker_img.'</center></div>';
  }

  //DEV Notes. WIll be in table. 3 parts. Top. Mid, Bottom.
  //I could come up with code notes but I'm avoiding header, body, footer as that might confuse people.

  $vidyen_wm_html_ouput = '<!-- Begin VidYen Output -->
  <table width="100%" align="'.$table_align.'">
  <tr>
    <td>
      '.$top_output.'
    </td>
  </tr>
  <tr>
    <td>
      '.$mid_output.'
    </td>
  </tr>
  <tr>
    <td>
      '.$bottom_output.'
    </td>
  </tr>
  </table>
  ';

  return $vidyen_wm_html_ouput; //This was made first. This is the output.
}
