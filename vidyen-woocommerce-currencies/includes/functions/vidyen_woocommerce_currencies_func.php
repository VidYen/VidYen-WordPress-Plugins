<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Not I removed the function to check, cause if its not there it doesn't do anything.

/*** Copper ***/
add_filter( 'woocommerce_currencies', 'add_vyps_copper_currency', 20,1 );

function add_vyps_copper_currency( $currencies )
{
   $currencies['COPPER'] = __( 'Copper Pieces', 'woocommerce' );
   return $currencies;
}

add_filter('woocommerce_currency_symbol', 'add_vyps_copper_currency_symbol', 20, 2);

function add_vyps_copper_currency_symbol( $currency_symbol, $currency )
{
   switch( $currency ) {
        case 'COPPER': $currency_symbol = 'Copper'; break;
   }
   return $currency_symbol;
}

 /*** VidYen ***/
add_filter( 'woocommerce_currencies', 'add_vyps_vidyen_currency', 21,1 );

function add_vyps_vidyen_currency( $currencies )
{
   $woocommerce_currencies_parsed_array = vidyen_woocommerce_currencies_settings();
   $index = 1; //Lazy coding but easier to copy and paste stuff.

   $pull_currency_name  = $woocommerce_currencies_parsed_array[$index]['currency_name'];

   $currencies['VYPS'] = __( $pull_currency_name, 'woocommerce' );
   //$currencies['VIDYEN'] = __( 'VidYen', 'woocommerce' );
   return $currencies;
}

add_filter('woocommerce_currency_symbol', 'add_vyps_vidyen_currency_symbol', 22, 2);

function add_vyps_vidyen_currency_symbol( $currency_symbol, $currency )
{
    $woocommerce_currencies_parsed_array = vidyen_woocommerce_currencies_settings();
    $index = 1; //Lazy coding but easier to copy and paste stuff.
    $pull_currency_symbol  = $woocommerce_currencies_parsed_array[$index]['currency_symbol'];
    //$currency_symbol = $pull_currency_symbol;

   switch( $currency )
   {

     /*case 'VIDYEN':

      break;*/
     case 'VYPS': $currency_symbol = "$pull_currency_symbol"; break;
   }

   return $currency_symbol;
}
