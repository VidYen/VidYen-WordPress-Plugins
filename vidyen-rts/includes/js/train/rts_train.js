//Sets the method to update to the refresh I'm debatin gto make this universial or by point.
function rts_train_soldiers()
{
  jQuery(document).ready(function($)
  {
   var data =
   {
     'action': 'vidyen_rts_train_soldiers_action',
   };
   // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
   jQuery.post(ajaxurl, data, function(response)
   {
     output_response = JSON.parse(response);
     //MMO Pull
     rts_train_system_message = output_response.system_message;
     rts_train_soldiers_story = output_response.mission_story;
     rts_train_soldiers_loot = output_response.mission_loot;

     rts_train_money_spent = output_response.money_spent;
     rts_train_soldiers_trained = output_response.soldiers_trained;

     rts_train_time_left = parseFloat(output_response.time_left); //I'm going to assume that if we run this a mission

     console.log(rts_train_system_message); //Yes I know I console log too much.

     //This gives the sad story of the soldiers
     document.getElementById('mission_output').innerHTML = rts_train_soldiers_story;

     //yeah I'm going to mess with formatting here because i don't want a really long string wide.
     var train_loot_table = 'Soldiers trained: ' + soldier_icon + ' ' + rts_train_soldiers_trained;

     document.getElementById('train_output').innerHTML = train_loot_table;

     train_soldiers_time_left(); //This should work now that we actually got time left after running command
   });
  });


  pull_rts_bal(); //If button is pushed might as well pull
}
