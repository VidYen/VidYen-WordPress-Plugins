<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//This function is designed to see if a refer code is actually a reer and return the user id it is FROM
//AND check to see if it exists (in case invalid or user id was deleted a while back)
//Also it checks to make sure you aren't putting in yoru own code. NO CHEATING
//Ok this checks the user id. But not if.

/*** REFER NAME FUNCTION ***/
function vyps_current_refer_name_func($user_id)
{
  //Set variables
  //Get current user Id obviously
  $current_user_id = $user_id; //This is passed via the function not because of the function. Debating if this is needed to check, but we will be burn that bridge when we get to it.

  //These two should be blank if none found.
  $display_refer = '';
  $current_refer_name = '';
  $message_output = '';

  //WE functionized this. This should output encode64
  $user_id = $current_user_id;
  $user_refer = vyps_create_refer_func($user_id);

  //This is hardcoded, but the label we are going to cram into the usermeta table
  $key = 'vyps_current_refer';

  //NO WIRE ARRAYS! Only one value at all time. Unless someone messed something up somewhere.
  $single = TRUE;

  //Ok now we can get it from the meta post post.
  $current_refer = get_user_meta( $current_user_id, $key, $single ); //the user_meta should be most up to date now.
  $current_refer_user_id = vyps_current_refer_func($current_user_id); //Basically we


  //Checking to see if function returned and ID.
  if ( $current_refer_user_id != 0 )
  {
    //Get that data!
    $current_refer_data = get_userdata( $current_refer_user_id );

    //This following data call should work. In theory.
    $current_refer_name = $current_refer_data->display_name; //Just the display name. I know I hate -> but its having issues.
  }
  else
  {
    //If not found, then well
    $current_refer_name = ''; //Needs to be set to blank. The other function will decide what to do if it returns blank.
    $display_refer = '';
  }

  return $current_refer_name;

}
