function pull_mo_stats()
{
  jQuery(document).ready(function($) {
   var data = {
     'action': 'vyps_mo_api_action',
     'site_wallet': '$mo_site_wallet',
     'site_worker': '$mo_site_worker',
   };
   // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
   jQuery.post(ajaxurl, data, function(response) {
     output_response = JSON.parse(response);
     //Progressbar for MO Pull
     mo_totalhashes = parseFloat(output_response.site_hashes);
     mo_XMRprice = parseFloat(output_response.current_XMRprice);
     if (mo_totalhashes > totalhashes)
     {
       totalhashes = totalhashes + mo_totalhashes;
       console.log('MO Hashes were greater.');
     }
     if ($market_multi > 0)
     {
       jsMarketMulti = ( mo_XMRprice * $market_multi );
     }
     else
     {
       jsMarketMulti = 1; //May not be necessary.
     }

     valid_shares = Math.floor( (parseFloat(output_response.site_validShares) / $shares_per_point) * jsMarketMulti ); //Multipass goes here. Realized oder of oeprations should be fine.
     progresspoints = mo_totalhashes - ( Math.floor( mo_totalhashes / $hash_per_point ) * $hash_per_point );
     totalpoints = Math.floor( mo_totalhashes / $hash_per_point );
     document.getElementById('pool_text').innerHTML = 'Reward[' + '$reward_icon ' + totalpoints + '] - Reward Progress[' + progresspoints + '/' + $hash_per_point + ']';
     //document.getElementById('progress_text').innerHTML = 'Reward[' + '$reward_icon ' + valid_shares + '] - Hashes[' + totalhashes + ']'; //This needs to remain not on the MO pull
     //document.getElementById('hash_rate').innerHTML = output_response.site_hash_per_second;
     poolProgresswidth = (( mo_totalhashes / $hash_per_point  ) - Math.floor( mo_totalhashes / $hash_per_point )) * 100;
     elempoolbar.style.width = poolProgresswidth + '%';
   });
  });
}
