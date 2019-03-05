<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//This function is designed to see if a refer code is actually a reer and return the user id it is FROM
//AND check to see if it exists (in case invalid or user id was deleted a while back)
//Also it checks to make sure you aren't putting in yoru own code. NO CHEATING
//Ok this checks the user id. But not if.

/*** IS REFER FUNCTION ***/
function vyps_current_refer_func($user_id)
{
  //NOTE: I went with variable pass. As we needed to check to post to see if it was valid before entering before it pulled the meta.

  //This is hardcoded, but the label we are going to cram into the usermeta table
  $key = 'vyps_current_refer';

  //NO WIRE ARRAYS! Only one value at all time. Unless someone messed something up somewhere.
  $single = TRUE;

  //Now we pull what the users current referr was
  $current_refer = get_user_meta( $user_id, $key, $single );

  //Checking to see if the variable is empty or not
  if (!empty($current_refer))
  {
    //Ok we take it back out again and base64_decode() it;
    //NOTE: I intvaled it because when people put in crap, it still has to be checked. In theory it will force a value that can be divided by 256
    //But will return a non-user. Unless you have millions of users? Why are you using WordPress then?
    $current_refer_user_id = (intval(base64_decode($current_refer)) / 256); //Making it a bet less obvious with the base 64, but please don't take me as a security expert -Felty

    //I'm going to check trim it what we pull from meta
    //DEBUG //$current_refer_user_id = trim($current_refer, "REFER"); //Tear that string down Mr. Gorbachev! Originally, you could type it in. Now it has to be exact.

    //And then check it if it actually works and that is a users.
    $current_refer_data = get_userdata( $current_refer_user_id ); //This is an array btw. See WP codex for details.

    //OK if this is empty because it returned nothing, then we have issues
    if ( is_wp_error($current_refer_data) OR $current_refer_data == false OR $current_refer_data == '')
    {
      //If it gives an error then it was not valid. Or if was a zero or false to begin withBack to zero here as well.
      //Or blank but not empty
      return 0;
    }
    elseif($user_id == $current_refer_user_id)
    {
      return 0; //In case your users thought they'd be cute and set themselves as their own refer.
    }
    elseif(!empty($current_refer_user_id))
    {
      //This following data call should work. In theory.
      return $current_refer_user_id; //This should be the id that the miner sends to. Pretty much the purpose of this function.
    } //End of is_wp_error if chain

  } //End of !empty if.

  //So if nothing found the userid, I guess we throw out a zero with our hands up in a shrug and say "nothing returned"
  return 0; //There was no id. Return a 0 so the extra part does nothing
}
