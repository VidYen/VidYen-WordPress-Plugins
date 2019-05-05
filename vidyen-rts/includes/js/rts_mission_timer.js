//This sould be set on page load or when mission runs.
function sack_village_time_left()
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

     rts_sack_time_left = parseFloat(output_response.time_left); //I'm going to assume that if we run this a mission

     setTimeout(function()
       {
         sack_village_time_left(); //Ajax needs 5 seconds (maybe?)
       }, 5000);

   });
  });

  //simple check to see if running already.
  //And then prevent from running further.
  if (pillage_timer_check > 0)
  {
    console.log('Pillage timer greater than 0!');
    return;
  }

  console.log('Pillage timer 0!');

  var elem = document.getElementById("raidVillageCoolDownTimer");
  var time_progress = 300 - rts_sack_time_left;

  var width = Math.floor( time_progress / 300 ) * 100;
  var sackid = setInterval(sackframe, 1000);
  //var id = setInterval(frame, 1000);
  function sackframe()
  {
   if (time_progress >= 300)
   {
     clearInterval(sackid);
     //document.getElementById('mission_output').innerHTML = "The villages are ready for pillaging! It's raiding season!";
     //document.getElementById('loot_output').innerHTML =  "Time for looting!";
     pillage_timer_check = 0;
   }
   else
   {
     width++;
     rts_sack_time_left = rts_sack_time_left - 1;
     time_progress++;
     width = Math.floor( (time_progress / 300 ) * 100);
     elem.style.width = width + '%';
     document.getElementById('countdown_time_left').innerHTML = 'You have ' + rts_sack_time_left + ' seconds left before you can loot again!';
     //This gives the sad story of the soldiers
     //document.getElementById('mission_output').innerHTML = "You must wait until local villages recover before taking advantage of them.";
     //document.getElementById('loot_output').innerHTML =  "You need to wait " + rts_sack_time_left +" seconds before pillaging again.";
     pillage_timer_check = 1; //if it counts it runs. It won't go false until above runs out.
     console.log('Pillage timer is 1 and sack time is ' + rts_sack_time_left);
   }
  }
}
