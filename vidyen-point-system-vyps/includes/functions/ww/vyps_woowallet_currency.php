<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//I needed a way to add my own currency in WooCommerce. I'm going to just use copper for the first. -Felty

//I needed a way to add my own currency in WooCommerce. I'm going to just use copper for the first. -Felty

/**
 * Custom currency and currency symbol
 *
 * Copper
 * Silver
 * Gold
 * Gems
 * VidYen
 *
 */

 /*** Copper ***/
add_filter( 'woocommerce_currencies', 'add_vyps_copper_currency' );

function add_vyps_copper_currency( $currencies )
{
     $currencies['COPPER'] = __( 'Copper Pieces', 'woocommerce' );
     return $currencies;
}

add_filter('woocommerce_currency_symbol', 'add_vyps_copper_currency_symbol', 10, 2);

function add_vyps_copper_currency_symbol( $currency_symbol, $currency )
{
     switch( $currency ) {
          case 'COPPER': $currency_symbol = 'Copper'; break;
     }
     return $currency_symbol;
}

/*** Silver ***/
add_filter( 'woocommerce_currencies', 'add_vyps_silver_currency' );

function add_vyps_silver_currency( $currencies )
{
    $currencies['SILVER'] = __( 'Silver Pieces', 'woocommerce' );
    return $currencies;
}

add_filter('woocommerce_currency_symbol', 'add_vyps_silver_currency_symbol', 10, 2);

function add_vyps_silver_currency_symbol( $currency_symbol, $currency )
{
    switch( $currency ) {
         case 'SILVER': $currency_symbol = 'Silver'; break;
    }
    return $currency_symbol;
}

/*** Gold ***/
add_filter( 'woocommerce_currencies', 'add_vyps_gold_currency' );

function add_vyps_gold_currency( $currencies )
{
    $currencies['GOLD'] = __( 'Gold Pieces', 'woocommerce' );
    return $currencies;
}

add_filter('woocommerce_currency_symbol', 'add_vyps_gold_currency_symbol', 10, 2);

function add_vyps_gold_currency_symbol( $currency_symbol, $currency )
{
    switch( $currency ) {
         case 'GOLD': $currency_symbol = 'Gold'; break;
    }
    return $currency_symbol;
}

/*** Gems ***/
add_filter( 'woocommerce_currencies', 'add_vyps_gems_currency' );

function add_vyps_gems_currency( $currencies )
{
    $currencies['GEMS'] = __( 'Gems', 'woocommerce' );
    return $currencies;
}

add_filter('woocommerce_currency_symbol', 'add_vyps_gems_currency_symbol', 10, 2);

function add_vyps_gems_currency_symbol( $currency_symbol, $currency )
{
    switch( $currency ) {
         case 'GEMS': $currency_symbol = 'Gems'; break;
    }
    return $currency_symbol;
}

/*** VidYen ***/
add_filter( 'woocommerce_currencies', 'add_vyps_vidyen_currency' );

function add_vyps_vidyen_currency( $currencies )
{
    $currencies['VIDYEN'] = __( 'VidYen', 'woocommerce' );
    return $currencies;
}

add_filter('woocommerce_currency_symbol', 'add_vyps_vidyen_currency_symbol', 10, 2);

function add_vyps_vidyen_currency_symbol( $currency_symbol, $currency )
{
    switch( $currency ) {
         case 'VIDYEN': $currency_symbol = 'V¥'; break;
    }
    return $currency_symbol;
}
