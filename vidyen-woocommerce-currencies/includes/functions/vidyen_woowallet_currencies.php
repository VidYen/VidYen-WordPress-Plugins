<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//If WooCommerce is installed then run this function.

if (vidyen_woocommerce_check())
{
  $pull_currency_name = 'VidYen';
  $pull_currency_symbol = 'V¥';

 /*** VidYen ***/
 add_filter( 'woocommerce_currencies', 'add_vyps_vidyen_currency' );

 function add_vyps_vidyen_currency( $currencies )
 {
     $currencies['VIDYEN'] = __( $pull_currency_name, 'woocommerce' );
     return $currencies;
 }

 add_filter('woocommerce_currency_symbol', 'add_vyps_vidyen_currency_symbol', 10, 2);

 function add_vyps_vidyen_currency_symbol( $currency_symbol, $currency )
 {
     switch( $currency )
     {
          case 'VIDYEN': $currency_symbol = $pull_currency_symbol; break;
     }
     return $currency_symbol;
 }
}
