<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Rather than making the point exchange shortcode even larger
//I am moving the WooWallet move function here.

/*** WOOWALLET CREDIT FUNCTION ***/
function vyps_woowallet_credit_func( $atts ) {

  //Check if user is logged in.
  //I've decided that the ! bothers me. Readability over efficiency.
  if ( is_user_logged_in() == FALSE ) {

    return; //GET OUT!

  }

  $atts = shortcode_atts(
		array(
				'firstid' => '0',
				'secondid' => '0',
				'outputid' => '0',
				'firstamount' => '0',
				'secondamount' => '0',
				'outputamount' => '0',
        'refer' => 0,
				'days' => '0',
				'hours' => '0',
				'minutes' => '0',
        'symbol' => '',
        'amount' => 0,
        'from_user_id' => 0,
        'to_user_id' => 0,
        'fee' => 0,
        'comment' => '',
        'skip_confirm' => true,
        'mobile' => false,
        'woowallet' => false,
		), $atts, 'vyps-pe' );

    $user_id = get_current_user_id(); //Since this doesn't carry over from the shortcode $atts (thank god hard ware is improving exponentially)
    $amount = floatval($atts['outputamount']); //desintation amount. Just making sure its numeric
    $details = 'VYPS Transfer';

    //Well this was 100% easier. I should have did this years ago.
    //Direct WooWallet Calls

    woo_wallet()->wallet->credit($user_id, $amount, $details);

    return 1; //Let them know it worked. But its an action not a value.

}
