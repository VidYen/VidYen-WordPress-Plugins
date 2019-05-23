//This sould be set on page load or when mission runs.
function train_soldiers_time_left()
{
  //simple check to see if running already.
  //And then prevent from running further.
  if (train_soldiers_timer_check > 0)
  {
    console.log('train_soldiers timer greater than 0!');
    return;
  }

  console.log('train_soldiers timer 0!');

  var elem = document.getElementById("hiresoldiersCoolDownTimer");
  var time_progress = 300 - rts_train_time_left;

  var width = Math.floor( time_progress / 300 ) * 100;
  var trainid = setInterval(trainframe, 1000);
  //var id = setInterval(frame, 1000);
  function trainframe()
  {
   if (time_progress >= 300)
   {
     clearInterval(trainid);
     //document.getElementById('mission_output').innerHTML = "The villages are ready for pillaging! It's raiding season!";
     //document.getElementById('loot_output').innerHTML =  "Time for looting!";
     train_soldiers_timer_check = 0;
     document.getElementById('train_soldiers_button').disabled = false;
     document.getElementById('train_soldiers_button').value = 'Speak to village Elder';
     document.getElementById('train_soldiers_countdown_time_left').innerHTML = "Let us go and talk with dreary common folk.!";
   }
   else
   {
     width++;
     rts_train_time_left = rts_train_time_left - 1;
     time_progress++;
     width = Math.floor( (time_progress / 300 ) * 100);
     elem.style.width = width + '%';
     document.getElementById('train_soldiers_countdown_time_left').innerHTML = 'You have ' + rts_train_time_left + ' seconds left before you arrive at new village!';
     //This gives the sad story of the soldiers
     //document.getElementById('mission_output').innerHTML = "You must wait until local villages recover before taking advantage of them.";
     //document.getElementById('loot_output').innerHTML =  "You need to wait " + rts_train_time_left +" seconds before pillaging again.";
     train_soldiers_timer_check = 1; //if it counts it runs. It won't go false until above runs out.
     //console.log('train_soldiers timer is 1 and train time is ' + rts_train_time_left);
     document.getElementById('train_soldiers_button').disabled = true;
     document.getElementById('train_soldiers_button').value = 'Resting';
   }
  }
}
