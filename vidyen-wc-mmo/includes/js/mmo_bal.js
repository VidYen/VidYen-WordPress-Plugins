//Sets the method to update to the refresh I'm debatin gto make this universial or by point.
function pull_mmo_stats()
{
  jQuery(document).ready(function($)
  {
   var data =
   {
     'action': 'vyps_mmo_bal_api_action',
   };
   // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
   jQuery.post(ajaxurl, data, function(response)
   {
     output_response = JSON.parse(response);
     //MMO Pull
     mmo_point_balance = parseFloat(output_response.point_balance); //To remove the number commands remove the .toLocaleString
     mmo_point_balance_string = mmo_point_balance.toLocaleString('en');

     //Check to see if wocommerce installed
     if (woo_installed == 1)
     {
       ww_point_balance = output_response.ww_balance;
     }


     if (mmo_point_balance > 0)
     {
       //console.log('Point Balance is: ' + mmo_point_balance); //Remove soon
       document.getElementById('vyps_points').innerHTML = mmo_point_balance_string; //This needs to remain not on the MO pull

       //Checking to see if woo installed and if not will display.
       if (woo_installed == 1)
       {
          document.getElementById('ww_points').innerHTML = ww_point_balance; //This needs to remain not on the MO pull
       }

     }
     else
     {
       //console.log('Point Balance is: 0'); //Remove soon
     }
   });
  });
}

window.setInterval(function()
{
/// call your function here
pull_mmo_stats();
}, 5000);
