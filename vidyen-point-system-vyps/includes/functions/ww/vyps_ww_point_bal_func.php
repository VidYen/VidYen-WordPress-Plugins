<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
** Advanced woowallet options
**
*/

/*** WOOWALLET BALANCE FUNCTION ***/
function vyps_ww_point_bal_func($user_id)
{
  $woo_balance = woo_wallet()->wallet->get_wallet_balance($user_id);

  return $woo_balance; //With balance, there should be a number returned.
}
