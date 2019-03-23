<?php

//Wannads pro_check feature. This actually allows post back
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function vyps_wannads_pro_check_func()
{

  //If you bought the $30 pro version it will do it.
  if (function_exists('vyps_flag_pro_2019'))
  {
    //OK it does exist so lets run it.
    $pro_result = intval(vyps_flag_pro_2019());
    return $pro_result; //It should be a 1 or a 0
  }

  //Otherwise, we just need the referral option.
  //This is just proving you have a referral code
  if (function_exists('vyps_flag_pro_wannads'))
  {
    //OK it does exist so lets run it.
    $pro_result = intval(vyps_flag_pro_wannads());
    return $pro_result; //It should be a 1 or a 0
  }
}

//no Need for pro check shortcode... That was debugging
