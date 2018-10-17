<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//This is a decod for the base64 code the user sees
//I had need for a system to show user ids to other user
//Without them simply typing in 1 to 100 to figureout who they are giving it to

/*** CREATE UID DECODE FUNCTION ***/
function vyps_create_refer_func($uid_code) {

  $user_id = (intval(base64_decode($uid_code)) / 256); //Making it a bet less obvious with the base 64, but please don't take me as a security expert -Felty

  //Checking to see if the variable is empty or not
  if (!empty($user_id)) {

    //OK if this is empty because it returned nothing, then we have issues
    if ( is_wp_error($user_id) OR $user_id == false OR $user_id == ''){

      //If it gives an error then it was not valid. Or if was a zero or false to begin withBack to zero here as well.
      //Or blank but not empty
      return 0;

    } elseif(!empty($user_id)) {

      //This following data call should work. In theory.
      return $user_id; //This should be the id that the miner sends to. Pretty much the purpose of this function.

    } //End of is_wp_error if chain

  } //End of !empty if.

  //So if nothing found the userid, I guess we throw out a zero with our hands up in a shrug and say "nothing returned"
  return 0; //There was no id. Return a 0 so the extra part does nothing

}
