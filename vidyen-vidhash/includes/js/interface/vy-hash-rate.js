//Refresh the MO
function vidyen_timer()
{
  //var totalhashes = 0; //NOTE: This is a notgiven688 variable.
  var mo_totalhashes = 0;
  var prior_totalhashes = 0;
  var hash_per_second_estimate = 0;
  var reported_hashes = 0;
  var current_algo = "";


  //Should call ajax every 30 seconds
  var ajaxTime = 1;
  var id = setInterval(vidyen_TimeFrame, 1000); //1000 is 1 second
  function vidyen_TimeFrame()
  {
    if (ajaxTime >= 30)
    {
      ajaxTime = 1;
    }
    else
    {
      //Hash work
      hash_difference = totalhashes - prior_totalhashes;
      hash_per_second_estimate = (hash_difference);
      reported_hashes = Math.round(totalhashes);
      prior_totalhashes = totalhashes;

      document.getElementById('hash_count').innerHTML = 'Hashes Worked: ' + reported_hashes;
      if (job == null)
      {
        current_algo = "None";
      }
      else
      {
        current_algo = job.algo;
      }
      document.getElementById('hash_rate').innerHTML = ' ' + hash_per_second_estimate + ' H/s' + ' [' + current_algo + ']';
    }
  }
}
