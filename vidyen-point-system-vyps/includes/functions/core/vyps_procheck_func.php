<?php

//Procheck function. Technically a shortcode but not to be used by admins for any real use other than to turn off ads.

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function vyps_procheck_func($atts)
{

  //This should need no shortcode attributes by default
  //But its useful to just check if we have.
  //Pulling the shortcode $atts from previous function. Saves some time.

  $atts = shortcode_atts(
    array(
        'pro' => '',
    ), $atts, 'vyps-procheck' );

  $pro_check_string = $atts['pro'];

  //NOTE: Doing a procheck install that ads a function
  if (function_exists('vyps_flag_pro_2019'))
  {

    //return 1;

    //OK it does exist so lets run it.
    $pro_result = intval(vyps_flag_pro_2019());
    return $pro_result; //It should be a 1 or a 0

  }

  //If it doesn't work, then just need to fall back on the legacy code.

  //Need to return out if the strlen() is less than 6. Or greater than six. No exception. We boot out! Don't even bother checking strings.
  //Always must be 6. No more. No less. The key is always six.
  if (strlen($pro_check_string) != 6 )
  {

    return 0; //False return!

  }

  //256-SHA broken up into rotations.
  $current_hash_string = "EFEEF1"
   . "274E54"
   . "CE6787"
   . "119CEF"
   . "05AB89"
   . "CDCF87"
   . "224E8F"
   . "92D573"
   . "4B533D"
   . "0C0C5D"
   . "399946"
   . "13EFE1"
   . "08D5FF"
   . "03F166"
   . "E08718"
   . "114F4F"
   . "D0457C"
   . "A68A8E"
   . "772E4A"
   . "29A4A2"
   . "D7DE5D"
   . "8C1187"
   . "6278E0"
   . "2C5C37"
   . "2FE26B"
   . "E2DC17"
   . "540B6B"
   . "07B9B0"
   . "1235BF"
   . "8789BC"
   . "447BB6"
   . "03945B"
   . "CD7561"
   . "C45D6F"
   . "F1FBCF"
   . "D74F3A"
   . "03CF97"
   . "623C2C"
   . "337985"
   . "730FA8"
   . "3B3D92"
   . "E226DF"
   . "F133S6";

   //Ere we go checking the stings for valid prochecks.
   if (strpos($current_hash_string, $pro_check_string) != FALSE)
   {

     return 1; //I suppose true would be well enough, but look. If they are looking at the PHP well....

   }

   else
   {

     return 0; //Failure! All you failures!

   }

}

//No shortcode required
