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

  var elem = document.getElementById("trainSolidersCoolDownTimer");
  var time_progress = 300 - rts_recruit_time_left;

  var width = Math.floor( time_progress / 300 ) * 100;
  var recruitid = setInterval(recruitframe, 1000);
  //var id = setInterval(frame, 1000);
  function recruitframe()
  {
   if (time_progress >= 300)
   {
     clearInterval(recruitid);
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
     rts_recruit_time_left = rts_recruit_time_left - 1;
     time_progress++;
     width = Math.floor( (time_progress / 300 ) * 100);
     elem.style.width = width + '%';
     document.getElementById('train_soldiers_countdown_time_left').innerHTML = 'You have ' + rts_recruit_time_left + ' before the laborers recover from trying to train!';
     //This gives the sad story of the soldiers
     //document.getElementById('mission_output').innerHTML = "You must wait until local villages recover before taking advantage of them.";
     //document.getElementById('loot_output').innerHTML =  "You need to wait " + rts_recruit_time_left +" seconds before pillaging again.";
     train_soldiers_timer_check = 1; //if it counts it runs. It won't go false until above runs out.
     //console.log('train_soldiers timer is 1 and recruit time is ' + rts_recruit_time_left);
     document.getElementById('train_soldiers_button').disabled = true;
     document.getElementById('train_soldiers_button').value = 'Resting';
   }
  }
}
