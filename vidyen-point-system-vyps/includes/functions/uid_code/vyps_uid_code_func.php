<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//This was copied and pasted from the user referreal
//I had need for a system to show user ids to other user
//Without them simply typing in 1 to 100 to figureout who they are giving it to

/*** CREATE UID CODE FUNCTION ***/
function vyps_uid_code_func($user_id) {

  //Checking to see if the user is actually a possible user number
  //If it isn't either of those. It tis input garbage!
  if ( !is_int($user_id) OR $user_id < 1 ){

    return 0; //If the id had nothing in it, then we just throw out a 0 as it is incorrect. I'd rather work with zero than something else.

  }

  //Need to add just a bit of non numerical database
  //$user_refer_prep_encode = 'REFER'. $user_id; //Testing something
  $user_refer_prep_encode = ($user_id * 256); //Trying something here. Making it less obvious is base64

  //From here we use the base64_encode() to ecode out I have no idea what it returns.
  $user_refer = base64_encode($user_refer_prep_encode);

  //And out it goes.
  return $user_refer;

}
