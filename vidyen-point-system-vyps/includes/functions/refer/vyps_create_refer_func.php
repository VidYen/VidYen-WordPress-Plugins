<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//This function is to create and return a referal code based on the current user
//For the refer shortcode. In theory one could feed a user id and get the refer code.
//But let us hide that so users are forced to interact rather than pick codes from a board.

/*** CREATE REFER FUNCTION ***/
function vyps_create_refer_func($user_id)
{
  //I Have decided to make this more generic.
  //Rather than having pull userid, I will pull from outside call
  //Which, at least if I am still around, will pull from $current_user_id
  //But it may not be always the case.

  //As was having issues, I thought might as well check to see if the $user_id was an int and actually 1 or greater.
  //If it isn't either of those. It tis input garbage!
  if ( !is_int($user_id) OR $user_id < 1 )
  {
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
