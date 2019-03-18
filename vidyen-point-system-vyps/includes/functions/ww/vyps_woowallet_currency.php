//I needed a way to add my own currency in WooCommerce. I'm going to just use copper for the first. -Felty

/**
 * Custom currency and currency symbol
 */

 /*** Copper ***/
add_filter( 'woocommerce_currencies', 'add_vyps_copper_currency' );

function add_vyps_copper_currency( $currencies )
{
     $currencies['COPPER'] = __( 'Copper Peices', 'woocommerce' );
     return $currencies;
}

add_filter('woocommerce_currency_symbol', 'add_vyps_copper_currency_symbol', 10, 2);

function add_vyps_copper_currency_symbol( $currency_symbol, $currency )
{
     switch( $currency ) {
          case 'COPPER': $currency_symbol = 'Cu'; break;
     }
     return $currency_symbol;
}

/*** Silver ***/
add_filter( 'woocommerce_currencies', 'add_vyps_silver_currency' );

function add_vyps_silver_currency( $currencies )
{
    $currencies['SILVER'] = __( 'Silver Peices', 'woocommerce' );
    return $currencies;
}

add_filter('woocommerce_currency_symbol', 'add_vyps_silver_currency_symbol', 10, 2);

function add_vyps_silver_currency_symbol( $currency_symbol, $currency )
{
    switch( $currency ) {
         case 'SILVER': $currency_symbol = 'Ag'; break;
    }
    return $currency_symbol;
}

/*** Gold ***/
add_filter( 'woocommerce_currencies', 'add_vyps_gold_currency' );

function add_vyps_gold_currency( $currencies )
{
    $currencies['GOLD'] = __( 'Currency name', 'woocommerce' );
    return $currencies;
}

add_filter('woocommerce_currency_symbol', 'add_vyps_gold_currency_symbol', 10, 2);

function add_vyps_gold_currency_symbol( $currency_symbol, $currency )
{
    switch( $currency ) {
         case 'GOLD': $currency_symbol = 'Au'; break;
    }
    return $currency_symbol;
}
