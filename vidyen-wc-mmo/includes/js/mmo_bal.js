//Sets the method to update to the refresh I'm debatin gto make this universial or by point.
function pull_mmo_stats()
{
  jQuery(document).ready(function($)
  {
   var data =
   {
     'action': 'vyps_mmo_bal_api_action',
     'point_id': point_id,
   };
   // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
   jQuery.post(ajaxurl, data, function(response)
   {
     output_response = JSON.parse(response);
     //MMO Pull
     mmo_point_balance = parseFloat(output_response.point_balance);
     if (mmo_point_balance > 0)
     {
       console.log('Point Balance is: ' + mmo_point_balance);
     }
     else
     {
       console.log('Point Balance is: 0');
     }
   });
  });
}

window.setInterval(function()
{
/// call your function here
pull_mmo_stats();
}, 5000);
