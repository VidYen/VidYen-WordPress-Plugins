<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//MO API - I thought best to functionalize this

//This will pull the MO price of XMR in USD
function vyps_mo_xmr_usd_api()
{
  /*** MoneroOcean Pool stat Gets***/
  //Site get
  $site_url = 'https://api.moneroocean.stream/pool/stats/'; //This does not change
  $pool_mo_response = wp_remote_get( $site_url );
  if ( is_array( $pool_mo_response ) )
  {
    $pool_mo_response = $pool_mo_response['body']; // use the content
    $pool_mo_response = json_decode($pool_mo_response, TRUE);
    if (array_key_exists('pool_statistics', $pool_mo_response)) //If pool stats don't exit, it won't work.
    {
      $pools_statistics = $pool_mo_response['pool_statistics'];
      //NOTE: it looks like we have to check each key on the way down as the API doesn't always feed on new workers
      if (array_key_exists('price', $pools_statistics))
      {
        $xmr_price = $pools_statistics['price']; //I'm removing the number format as we need the raw data.
        $xmr_usd_price = $xmr_price['usd'];
        $xmr_usd_price = floatval($xmr_usd_price);

        return $xmr_usd_price; //If there is a USD price return it. No formatting.
      }
    }
  }
  return 0; //If it failed it faliled
}
