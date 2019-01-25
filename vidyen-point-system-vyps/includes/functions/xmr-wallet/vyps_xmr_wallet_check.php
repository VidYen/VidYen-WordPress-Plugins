<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** Functionalized XMR wallet check since it might haeppen several times ***/

function vyps_xmr_wallet_check_func($wallet)
{
	//Might as well check to see if wallet is right length
	$wallet_len = strlen($wallet);

	//Wallets should always be longer than 90 character... 95, but
	if ($wallet_len < 90 )
	{
		return 3; //If we get a 3 it means len issue
	}

	//Checkj the first character
	$wallet_first_character = substr($wallet, 0, 1);

	if ($wallet_first_character == '4' OR $wallet_first_character == '8')
	{
		return 1; //This wallet is mostly valid except other typos.
	}
	else
	{
		//report that invalid validate
		return 2; //Got a first character issue. Return 2
	}

	return 0; //No clue what happened. Return a general 0
}
