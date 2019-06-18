//Sets the method to update to the refresh I'm debatin gto make this universial or by point.
function pull_rts_bal()
{
  jQuery(document).ready(function($)
  {
   var data =
   {
     'action': 'vidyen_rts_bal_api_action',
     'user_id': user_id,
   };
   // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
   jQuery.post(ajaxurl, data, function(response)
   {
     output_response = JSON.parse(response);
     //RTS Pull
     //mmo_point_balance = parseFloat(output_response.point_balance); //To remove the number commands remove the .toLocaleString
     //mmo_point_balance_string = mmo_point_balance.toLocaleString('en');
     rts_currency_balance = parseFloat(output_response.currency_balance);
     rts_wood_balance = parseFloat(output_response.wood_balance);
     rts_iron_balance = parseFloat(output_response.iron_balance);
     rts_stone_balance = parseFloat(output_response.stone_balance);
     rts_light_soldier_balance = parseFloat(output_response.light_soldier_balance);
     rts_laborer_balance = parseFloat(output_response.laborer_balance);

     document.getElementById('currency_balance').innerHTML = rts_currency_balance;
     document.getElementById('wood_balance').innerHTML = rts_wood_balance;
     document.getElementById('iron_balance').innerHTML = rts_iron_balance;
     document.getElementById('stone_balance').innerHTML = rts_stone_balance;
     document.getElementById('light_soldier_balance').innerHTML = rts_light_soldier_balance;
     document.getElementById('laborer_balance').innerHTML = rts_laborer_balance;
   });
  });
}

window.setInterval(function()
{
/// call your function here
// I'm setting to 12 seconds to make less taxing on server
pull_rts_bal()
}, 12000);
