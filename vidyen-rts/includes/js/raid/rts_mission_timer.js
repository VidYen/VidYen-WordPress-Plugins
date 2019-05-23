//This sould be set on page load or when mission runs.
function sack_village_time_left()
{
  //simple check to see if running already.
  //And then prevent from running further.
  if (pillage_timer_check > 0)
  {
    console.log('Pillage timer greater than 0!');
    return;
  }

  console.log('Pillage timer 0!');

  var elem = document.getElementById("raidVillageCoolDownTimer");
  var time_progress = 180 - rts_sack_time_left;

  var width = Math.floor( time_progress / 180 ) * 100;
  var sackid = setInterval(sackframe, 1000);
  //var id = setInterval(frame, 1000);
  function sackframe()
  {
   if (time_progress >= 180)
   {
     clearInterval(sackid);
     //document.getElementById('mission_output').innerHTML = "The villages are ready for pillaging! It's raiding season!";
     //document.getElementById('loot_output').innerHTML =  "Time for looting!";
     pillage_timer_check = 0;
     document.getElementById('sack_button').disabled = false;
     document.getElementById('sack_button').value = 'Raid village!';
     document.getElementById('countdown_time_left').innerHTML = "It's raiding season!";

     //Show it back to normal
     document.getElementById("village_burning").style.display = 'none'; // hide burning
     document.getElementById("village_fine").style.display = 'block'; // show normal village
   }
   else
   {
     width++;
     rts_sack_time_left = rts_sack_time_left - 1;
     time_progress++;
     width = Math.floor( (time_progress / 180 ) * 100);
     elem.style.width = width + '%';
     document.getElementById('countdown_time_left').innerHTML = 'You have ' + rts_sack_time_left + ' seconds left before you can loot again!';
     //This gives the sad story of the soldiers
     //document.getElementById('mission_output').innerHTML = "You must wait until local villages recover before taking advantage of them.";
     //document.getElementById('loot_output').innerHTML =  "You need to wait " + rts_sack_time_left +" seconds before pillaging again.";
     pillage_timer_check = 1; //if it counts it runs. It won't go false until above runs out.
     //console.log('Pillage timer is 1 and sack time is ' + rts_sack_time_left);
     document.getElementById('sack_button').disabled = true;
     document.getElementById('sack_button').value = 'Resting';

     //Set village on fire
     document.getElementById("village_burning").style.display = 'block'; // hide burning
     document.getElementById("village_fine").style.display = 'none'; // show normal village
   }
  }
}
