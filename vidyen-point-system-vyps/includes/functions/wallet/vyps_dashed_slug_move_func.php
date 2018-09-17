<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Creating a function to tie into the PE to transfer from an admin designated user to current user
//It won't really return anything other than a sucess message.
//I feel like this could go really wrong on some ones site.
//This code includes no warranty and I'm pretty sure neither does Dashed Slug's code either.
//If you have no idea what this about...
//DS's code can be found here: https://wallets-phpdoc.dashed-slug.net/

/*** DASHED SLUG MOVE FUNCTION ***/
function vyps_dashed_slug_move_func( $atts ) {

  //Check if user is logged in.
  if ( !is_user_logged_in() ) {

    return; //GET OUT!

  }

  $atts = shortcode_atts(
    array(
        'symbol' => 'DOGE',
        'amount' => 1,
        'from_user_id' => 1,
        'to_user_id' => 2,
        'fee' => 0,
        'comment' => 'VYPS Transfer',
        'skip_confirm' => true,
    ), $atts, 'vyps-pe' );

    $reward_symbol = $atts['symbol'];  //Your coin name. I suppose this is DOGE or LTC. I'm leaving default DOGE as WCCW
    $reward_amount = $atts['amount']; //How much you want to reward. Well this is an API call and I assume DS santizied. WCCW!
    $source_bank_account = $atts['from_user_id']; //OK peeps this is the bank account. The admin makes a users and designates it the deposite account. Use it wisely. The admin can abuse one of his users if he wants or is terrible at security.
    $current_user_id = get_current_user_id(); //I'm only comfortable doing the current logged in user. Your users can use the DS interface for user to user trades if they want.
    $fee = $atts['fee']; //I put in 0, but if you want fee then set.
    $vyps_comment = $atts['VYPS Transfer']; //Letting you know it was a VYPS transfer. You can turn off i fyou want.
    $skip_confirm = $atts['skip_confirm']; //Up to admin i guess. But for me. I'm leaving it off.
    $bal_check = vyps_dashed_slug_bal_check_func($atts); //Check the balance again!

    if( $bal_check == 1){

      //NOTE: This only works if dashed slug API works and is installed.
      //I will neither check nor really provide support on this. As I think it's cool but its a bad idea to do.
      //It is like showing people how to make bump stocks and recreational nukes.
      do_action( 'wallets_api_move', array(
        'symbol' => $reward_symbol,
        'amount' => $reward_amount,
        'from_user_id' => $source_bank_account,
        'to_user_id' => $current_user_id,
        'fee' => $fee,
        'comment' =>   $vyps_comment,
        'skip_confirm' => $skip_confirm,
      ) );

      return 1; //Let them know it worked.

  } else {

    return 0; //The function did not work for some weird reason. Most likely balance issue.

  }

    //From my understanding it will throw an exception if the bank's wallet is empty.
    //I will make a check balance function so I know if the wallet has an issue then run this one.

}
