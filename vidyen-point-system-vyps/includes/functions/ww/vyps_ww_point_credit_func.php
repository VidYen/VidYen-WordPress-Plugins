<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
** Advanced point credit function direct.
** Using the WooWallet input funcitons since do not control that.
*/

/*** WOOWALLET CREDIT FUNCTION ***/
function vyps_ww_point_credit_func( $user_id, $ouput_amount, $reason )
{
    $amount = intval($ouput_amount);
    $details = sanitize_text_field($reason);

    //Well this was 100% easier. I should have did this years ago.
    //Direct WooWallet Calls

    woo_wallet()->wallet->credit($user_id, $amount, $details);

    return 1; //Let them know it worked. But its an action not a value.
}
