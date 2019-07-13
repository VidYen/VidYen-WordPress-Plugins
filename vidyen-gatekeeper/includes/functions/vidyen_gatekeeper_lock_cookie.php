<?php

add_action('the_content', 'vidyen_check_lock_cookie');
function vidyen_check_lock_cookie($content)
{
  $gatekeeper_parsed_array = vidyen_gatekeeper_settings();
  $index = 1; //Lazy coding but easier to copy and paste stuff.
  $gatekeeper_active = $gatekeeper_parsed_array[$index]['gatekeeper_active'];

  //If gate keeper is active they we run the below code.
  if ($gatekeeper_active == 1)
  {
    //This need to be set in both php functions and need to be the same.
    $cookie_name = "vidyengatekeeperconsent";
    $cookie_value = "consented";
    if(!isset($_COOKIE[$cookie_name]))
    {
      //Grab the values from array from SQL pull of the Gatekeeper settings
      $button_text  = $gatekeeper_parsed_array[$index]['button_text'];
      $disclaimer_text  = $gatekeeper_parsed_array[$index]['disclaimer_text'];
      $eula_text  = $gatekeeper_parsed_array[$index]['eula_text'];

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
      $disclaimer_text = '<div align="center">'.$disclaimer_text.'</div><br>';
      $consent_button_html = "
        <div align=\"center\"><button onclick=\"createconsentcookie()\">$button_text</button></div>
        <script>
          function createconsentcookie() {
            jQuery(document).ready(function($) {
             var data = {
               'action': 'vidyen_gatekeeper_set_cookie_action',
             };
             // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
             jQuery.post(ajaxurl, data, function(response) {
               location.reload();
             });
            });
          }
        </script>";

        $eula_text = '<br><div>'.$eula_text.'</div>';

        $html_gatekeeper_output = $disclaimer_text.$consent_button_html.$eula_text;

      //$vidhash_consent_cookie_html = $disclaimer_text . $consent_button_html;
      //return $vidhash_consent_cookie_html;
      return $html_gatekeeper_output;
    }
  }
  return $content;
}
