<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Rather than making the point exchange shortcode even larger
//I am moving the WooWallet move function here.

/*** WOOWALLET BALANCE FUNCTION ***/
function vyps_woowallet_bal_func()
{
  //Check if user is logged in.
  //I've decided that the ! bothers me. Readability over efficiency.
  if (!is_user_logged_in())
  {
    return; //GET OUT!
  }

  $user_id = get_current_user_id(); //Since this doesn't carry over from the shortcode $atts (thank god hard ware is improving exponentially)

  $woo_balance = woo_wallet()->wallet->get_wallet_balance($user_id);

  return $woo_balance; //With balance, there should be a number returned.
}
