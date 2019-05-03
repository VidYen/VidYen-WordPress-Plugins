//Sets the method to update to the refresh I'm debatin gto make this universial or by point.
function rts_sack_village()
{
  jQuery(document).ready(function($)
  {
   var data =
   {
     'action': 'vidyen_rts_sack_village_action',
   };
   // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
   jQuery.post(ajaxurl, data, function(response)
   {
     output_response = JSON.parse(response);
     //MMO Pull
     rts_sack_system_message = output_response.system_message;
     rts_sack_mission_story = output_response.mission_story;
     rts_sack_mission_loot = output_response.mission_loot;

     console.log(rts_sack_system_message); //Yes I know I console log too much.

     document.getElementById('mission_output').innerHTML = rts_sack_mission_story; //This needs to remain not on the MO pull
     document.getElementById('loot_output').innerHTML = rts_sack_mission_loot;

   });
  });

  var dice_roll = Math.floor(Math.random() * 5);
  if (dice_roll == 0)
  {
    document.getElementById('sack_button').value = 'I hope you have something worth looting!';
  }
  else if (dice_roll == 1)
  {
    document.getElementById('sack_button').value = 'Whose being repressed now!';
  }
  else if (dice_roll == 2)
  {
    document.getElementById('sack_button').value = 'Rule by popular vote? What?!';
  }
  else if (dice_roll == 3)
  {
    document.getElementById('sack_button').value = 'Its raiding season!';
  }
  else if (dice_roll == 4)
  {
    document.getElementById('sack_button').value = 'Tally Hoe!';
  }

  pull_rts_bal(); //If button is pushed might as well pull

}
