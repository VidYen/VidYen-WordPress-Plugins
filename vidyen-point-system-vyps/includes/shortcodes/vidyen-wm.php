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
  $custom_wmp = $vy_wm_parsed_array[$index]['custom_wmp'];

  //Init:
  $top_output = '';
  $mid_output = '';
  $bottom_output = '';
  $table_align = 'left';

  //GRAPHICS
  $image_url_folder = plugins_url( 'images/', dirname(__FILE__) );
  $vidyen_login_worker_img = '<img src="'.$image_url_folder.'stat_vyworker_006.gif" style="height: 256px;">';

  //Cookie setup
  $cookie_name = "vidyenwmconsent";
  $cookie_value = "consented";

  if (!is_user_logged_in())
  {
    $table_align = 'center';
    $top_output = '<div>'.$login_text.'</div>';
    $mid_output = '<button id="login" style="width:100%;" onclick="vidyen_login_redirect()">Login</button>
    <script>
      function vidyen_login_redirect()
      {
        location.replace("'.$login_url.'")
      }
    </script>';
    $bottom_output = $vidyen_login_worker_img;
  }
  elseif (!isset($_COOKIE[$cookie_name]))
  {
    //NOTE: I've added [img][/img], [b][/b], [br][/br] for my own use. I'm thinking of adding links later
    //str_replace("world","Peter","Hello world!");

    //For $disclaimer_text
    //Images
    $disclaimer_text = str_replace("[img]",'<img src="',$disclaimer_text);
    $disclaimer_text = str_replace("[/img]",'">',$disclaimer_text);
    //Bold
    $disclaimer_text = str_replace("[b]",'<b>',$disclaimer_text);
    $disclaimer_text = str_replace("[/b]",'</b>',$disclaimer_text);
    //Line Breaks
    $disclaimer_text = str_replace("[br]",'<br>',$disclaimer_text);

    //For $eula_text
    //Images
    $eula_text = str_replace("[img]",'<img src="',$eula_text);
    $eula_text = str_replace("[/img]",'">',$eula_text);
    //Bold
    $eula_text = str_replace("[b]",'<b>',$eula_text);
    $eula_text = str_replace("[/b]",'</b>',$eula_text);
    //Line Breaks
    $eula_text = str_replace("[br]",'<br>',$eula_text);

    //For caps! Because I know someone is going to screw it up
    //For $disclaimer_text
    //Images
    $disclaimer_text = str_replace("[IMG]",'<img src="',$disclaimer_text);
    $disclaimer_text = str_replace("[/IMG]",'">',$disclaimer_text);
    //Bold
    $disclaimer_text = str_replace("[B]",'<b>',$disclaimer_text);
    $disclaimer_text = str_replace("[/B]",'</b>',$disclaimer_text);
    //Line Breaks
    $disclaimer_text = str_replace("[BR]",'<br>',$disclaimer_text);

    //For $eula_text
    //Images
    $eula_text = str_replace("[IMG]",'<img src="',$eula_text);
    $eula_text = str_replace("[/IMG]",'">',$eula_text);
    //Bold
    $eula_text = str_replace("[B]",'<b>',$eula_text);
    $eula_text = str_replace("[/B]",'</b>',$eula_text);
    //Line Breaks
    $eula_text = str_replace("[BR]",'<br>',$eula_text);

    //Let's have the disclaimer up front
    $top_output = '<div align="center">'.$disclaimer_text.'</div><br>';
    $mid_output = '<div align="center"><button onclick="createconsentcookie()">'.$button_text.'</button></div>';
    $mid_output .="<script>
        function createconsentcookie() {
          jQuery(document).ready(function($) {
           var data = {
             'action': 'vidyen_wm_set_cookie_action',
           };
           // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
           jQuery.post(ajaxurl, data, function(response) {
             location.reload();
           });
          });
        }
      </script>";

      $bottom_output = '<div align="left">'.$eula_text.'</div><br>';
  }
  elseif(isset($_COOKIE[$cookie_name]))
  {
    //NOTE Here is the meaty meat of the application.

    //First things first... Get the graphic.
    wp_parse_str($graphic_selection, $graphics_selection_arary);

    //Here we set the arrays of possible graphics. Eventually this will be a slew of graphis. Maybe holidy day stuff even.
    $count = 0; //we need to count how many are selected
    $graphic_list[0] = ''; //need to init.

    //NOTE: These have to checked each and not an elseif since they all could be true
    //We actually need to check each one. May not be the most efficient.
    if(intval($graphics_selection_arary['girl'])==1)
    {
      $count++;
      $graphic_list[$count] .= 'vyworker_001.gif';
    }

    //We actually need to check each one. May not be the most efficient.
    if(intval($graphics_selection_arary['guy'])==1)
    {
      $count++;
      $graphic_list[$count] .= 'vyworker_002.gif';
    }

    //We actually need to check each one. May not be the most efficient.
    if(intval($graphics_selection_arary['cyber'])==1)
    {
      $count++;
      $graphic_list[$count] .= 'vyworker_003.gif';
    }

    //We actually need to check each one. May not be the most efficient.
    if(intval($graphics_selection_arary['undead'])==1)
    {
      $count++;
      $graphic_list[$count] .= 'vyworker_004.gif';
    }

    //We actually need to check each one. May not be the most efficient.
    if(intval($graphics_selection_arary['peasant'])==1)
    {
      $count++;
      $graphic_list[$count] .= 'vyworker_005.gif';
    }

    //We actually need to check each one. May not be the most efficient.
    if(intval($graphics_selection_arary['youtube'])==1)
    {
      $count++;
      $graphic_list[$count] .= 'youtube';
    }

    //Pick the graphic via RNG. Oh the gods. I'm recycling code.
    if ($count >= 2)
    {
      $rand_choice =  mt_rand(1, $count);
      $current_graphic = $graphic_list[$rand_choice];
    }
    elseif ($count == 1)
    {
      $current_graphic = $graphic_list[1];
    }
    else
    {
      $current_graphic = 'vyworker_blank.gif';
    }

    $VYWM_worker_url = plugins_url( 'images/', dirname(__FILE__) ) . $current_graphic; //Now with dynamic images!
    $VYWM_stat_worker_url = plugins_url( 'images/', dirname(__FILE__) ) . 'stat_'. $current_graphic; //Stationary version!
    $VYPS_power_url = plugins_url( 'images/', dirname(__FILE__) ) . 'powered_by_vyps.png'; //Still technically vyps

    //This
    $VYPS_power_row = '<tr><td align="center"><a href="https://wordpress.org/plugins/vidyen-point-system-vyps/" target="_blank"><img src="'.$VYPS_power_url.'" alt="Powered by VYPS" height="28" width="290"></a></td></tr>';

    //OK going to do a shuffle of servers to pick one at random from top.
    if(empty($custom_wmp))
    {
      if ($current_wmp == 'igori.vy256.com:8256')
      {
        $server_name = array(
              array('igori.vy256.com:8256'),
              array('igori.vy256.com:8256'),
        );
      }
      elseif($current_wmp == 'savona.vy256.com:8183')
      {
        $server_name = array(
              array('savona.vy256.com:8183'), //2,0 2,1
              array('vesalius.vy256.com:8443'), //0,0 0,1
              array('daidem.vidhash.com:8443'), //1,0 1,1
              array('clarion.vidhash.com:8286'), //her own
              array('clarion.vidhash.com:8186'), //her own
        );
      }
      elseif($current_wmp == 'webminer.moneroocean.stream:443')
      {
        $server_name = array(
              array('webminer.moneroocean.stream:443'),

        );
      }
    }
    else
    {
      //This is the custom list.
      $server_name = array(
            array($custom_wmp),
      );
    }


    //Lets test
    $top_output = $VYWM_worker_url .' plus the '.$count;
    $mid_output = 'Start button here';
    $bottom_output = $server_name[0][0]; //Just the first one

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
    <td align="'.$table_align.'">
      '.$bottom_output.'
    </td>
  </tr>
  </table>
  ';

  return $vidyen_wm_html_ouput; //This was made first. This is the output.
}
