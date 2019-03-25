<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Recreating the original point system and moving it here.

/*** WOOWALLET BALANCE FUNCTION ***/
function vidyen_mmo_woowallet_bal_func($user_id)
{
  $woo_balance = woo_wallet()->wallet->get_wallet_balance($user_id);

  return $woo_balance; //With balance, there should be a number returned.
}
