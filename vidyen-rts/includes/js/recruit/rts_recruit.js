//Sets the method to update to the refresh I'm debatin gto make this universial or by point.
function rts_recruit_laborers()
{
  jQuery(document).ready(function($)
  {
   var data =
   {
     'action': 'vidyen_rts_recruit_laborers_action',
   };
   // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
   jQuery.post(ajaxurl, data, function(response)
   {
     output_response = JSON.parse(response);
     //MMO Pull
     rts_recruit_system_message = output_response.system_message;
     rts_recruit_laborers_story = output_response.mission_story;
     rts_recruit_laborers_loot = output_response.mission_loot;

     rts_recruit_money_spent = output_response.money_spent;
     rts_recruit_laborers_hired = output_response.laborers_hired;

     rts_recruit_time_left = parseFloat(output_response.time_left); //I'm going to assume that if we run this a mission

     console.log(rts_recruit_system_message); //Yes I know I console log too much.

     //This gives the sad story of the soldiers
     document.getElementById('mission_output').innerHTML = rts_recruit_laborers_story;

     //yeah I'm going to mess with formatting here because i don't want a really long string wide.
     var recruit_loot_table = 'Laborers Recruited: ' + laborer_icon + ' ' + rts_recruit_laborers_hired;

     document.getElementById('recruit_output').innerHTML = recruit_loot_table;

     recruit_laborers_time_left(); //This should work now that we actually got time left after running command
   });
  });


  pull_rts_bal(); //If button is pushed might as well pull
}
