<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Rather than making the point exchange shortcode even larger
//I am moving the WooWallet move function here.

/*** WOOWALLET MOVE FUNCTION ***/
function vyps_woowallet_move_func( $atts ) {

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

    $current_user_id = get_current_user_id(); //Since this doesn't carry over from the shortcode $atts (thank god hard ware is improving exponentially)
    $pt_dAmount = floatval($atts['outputamount']); //desintation amount. Just making sure its numerical.
    //All the other things probaly not even needed. If this is being called we know its going into WooWallet

    $ww_earn = $pt_dAmount; //Used as a reference.

    global $wpdb;
    $table_name_woowallet = $wpdb->prefix . 'woo_wallet_transactions';
    // I feel like if WooWallet coder realized balances were bad and logs were good, I wouldn't have to do the following
    // I'm pulling the max transaction_id for the user and then creating a new one with the balance + earn to get the new balance on new row

    //$last_trans_id = $wpdb->get_var( "SELECT max(transaction_id) FROM $table_name_woowallet WHERE user_id = $current_user_id");
    $last_trans_id_query = "SELECT max(transaction_id) FROM ". $table_name_woowallet . " WHERE user_id = %d";
    $last_trans_id_query_prepared = $wpdb->prepare( $last_trans_id_query, $current_user_id );
    $last_trans_id  = $wpdb->get_var( $last_trans_id_query_prepared );

    //return $last_trans_id; //this was 7
    //$new_trans_id = $last_trans_id + 1; //Not needed as i think its auto increment //$current_user_id

    //$old_balance = $wpdb->get_var( "SELECT sum(balance) FROM $table_name_woowallet WHERE user_id = $current_user_id AND transaction_id = $last_trans_id");
    $old_balance_query = "SELECT sum(balance) FROM ". $table_name_woowallet . " WHERE user_id = %d AND transaction_id = %d";
    $old_balance_query_prepared = $wpdb->prepare( $old_balance_query, $current_user_id, $last_trans_id );
    $old_balance  = $wpdb->get_var( $old_balance_query_prepared );


    //return $old_balance; //this was 1.01 which is correct
    $new_balance = $old_balance + $ww_earn;

    //return $new_balance; //this was 3.01 which is also correct so it means the feed is not working
    $data_ww = [
      //'blog_id' => '1',
      'user_id' => $current_user_id,
      'type' => 'credit',
      'balance' => $new_balance,
      'currency' => 'VYP',
      'details' => 'VYPS',
      //'deleted' => 0,
      //'date' => date('Y-m-d H:i:s'),
      'amount' => $ww_earn,
      ];

      //return $table_name_woowallet;

      $wpdb->insert($table_name_woowallet, $data_ww);

    return 1; //Let them know it worked.

}
