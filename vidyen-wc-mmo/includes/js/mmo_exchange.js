//Exchange Ajax code with confirmation button

//Confirm exchange button
function vidyen_confirm_exchange()
{
  if (confirm("Do you wish to exchange copper for silver?"))
  {
    vidyen_mmo_exchange();
  }
  else
  {
    //Nothing. Placeholder for lader.
  }
}

//Sets the method to update to the refresh I'm debatin gto make this universial or by point.
function vidyen_mmo_exchange()
{
  var results_string = '';
  jQuery(document).ready(function($)
  {
   var data =
   {
     'action': 'vyps_mmo_exchange_api_action',
     //'multi': multi_exchange,
   };
   // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
   jQuery.post(ajaxurl, data, function(response)
   {
     output_response = JSON.parse(response);
     point_balance = parseFloat(output_response.point_balance);
     needed_balance = parseFloat(output_response.needed_balance);
     deduct_result = output_response.deduct_result;
     add_result = output_response.add_result;

     if ( needed_balance > 0 )
     {
       results_string = 'Need ' + needed_balance + ' more to exchange!';
       success_message = 'Not enough!';
     }

     if ( add_result > 0 )
     {
       var numdate = Date(Date.now());
       humdate = numdate.toString();
       results_string = 'Points exchanged! ' + humdate;
       pull_mmo_stats(); //Make sure the balances are updated.
       success_message = 'Sucess!';
     }

     window.alert(results_string);
     document.getElementById('exchange_results').innerHTML = success_message; //This needs to remain not on the MO pull
   });
  });
}
