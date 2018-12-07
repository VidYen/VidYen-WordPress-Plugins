<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*** RNG Wheel Game ***/
//Here we go. Will be AJAX to simply click and bet points on wheel spin rewards.
//Shortcode etc. I'm not sure if this should be a function per se, but I feel that this should go in base. As its damn interesting concept.
//Going to copy the Raffle format except there will be no PvP but PvE (well player vs house). I kind of forgot that we can just... Well... Print points...
//Players betting on the house would be interesting but not really needed.

/*** The RNG Wheel Function ***/
//LICENSE: MIT code used from https://github.com/mmalmi/gamewheel

function vyps_rng_wheel_func( $atts )
{
  //Get the url for the wheel js
  $vyps_wheel_jquery_folder_url = plugins_url( 'js/jquery/', __FILE__ );
  $vyps_wheel_jquery_folder_url = str_replace('shortcodes/', '', $vyps_wheel_jquery_folder_url); //having to reomove the folder depending on where you plugins might happen to be
  $vyps_wheel_js_url =  $vyps_wheel_jquery_folder_url . 'jquery-1.8.3.min.js';

  $vyps_rng_wheel_html_output = "
    <style type=\"text/css\">
    body {
    	background: black;
    }
    #gameCanvas {
    }
    </style>

    <script src=\"$vyps_wheel_js_url\"></script>
    <table>
      <canvas width=\"256\" height=\"256\" id=\"gameCanvas\">
      </canvas>
    </table>

    <script type=\"text/javascript\">
    	window.requestAnimFrame = (function(callback) {
            return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame ||
            function(callback) {
              window.setTimeout(callback, 1000 / 60);
            };
          })();
    	var gameList = {
    		\"a1\" : 6.25,
        \"a1/2\" : 6.25,
        \"a1 1/2\" : 6.25,
        \"a2\" : 6.25,
        \"b1\" : 6.25,
        \"b3/4\" : 6.25,
        \"b1 3/4\" : 6.25,
        \"b3\" : 6.25,
        \"c1\" : 6.25,
        \"c1/2\" : 6.25,
        \"c1 1/2\" : 6.25,
        \"c0\" : 6.25,
        \"d1\" : 6.25,
        \"d3/4\" : 6.25,
        \"d1 3/4\" : 6.25,
        \"d0\" : 6.25
    	}
    	var totalGameScores = 0;
    	for (var game in gameList) {
    		totalGameScores += gameList[game];
    	}
    	var sectorColors = [
    		\"#f9a11b\",
    		\"#ca5500\",
        \"#f9a11b\",
    		\"#ca5500\",
        \"#f9a11b\",
    		\"#ca5500\",
        \"#f9a11b\",
    		\"#ca5500\",
        \"#f9a11b\",
        \"#ca5500\",
        \"#f9a11b\",
        \"#ca5500\",
        \"#f9a11b\",
        \"#ca5500\",
        \"#f9a11b\",
        \"#ca5500\"
    	]
    	function getRandomRollSpeed() {
        var vyps_response_rng = \"global\";
        vyps_response_rng = 1;
        jQuery(document).ready(function($) {
          var data = {
            'action': 'vyps_spin_wheel_action',
            'whatever': '0',
          };

          // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
          jQuery.post(ajaxurl, data, function(response) {
            vyps_response_rng = response;
          });
        });
        return vyps_response_rng;
    	}

    	var rollSpeedDeceleration = 0.003;
    	var isSpinning = false;
    	var stopSpinning = false;
    	var centerX = 0;
    	var centerY = 0;
    	var radius = 170;
    	var currentColor = 0;
    	var canvas = document.getElementById('gameCanvas');
    	var context = canvas.getContext('2d');
    	var rotatingCanvas = document.createElement('canvas');
    	var rotatingContext = rotatingCanvas.getContext('2d');
    	function nextColor() {
    		currentColor = currentColor + 1 >= sectorColors.length ? 0 : currentColor + 1;
    		return sectorColors[currentColor];
    	}
    	function setCanvasWidth() {
    		canvas.width = (256 * 2) - 20;
    		canvas.height = (256 * 2) - 20;
    		rotatingCanvas.width = canvas.width;
    		rotatingCanvas.height = canvas.height;
    		centerX = canvas.width / 2;
    		centerY = canvas.height / 2;
    		radius = Math.min(canvas.width, canvas.height) * 2 / 5;
    	}
    	function drawWheelAxis() {
    		rotatingContext.beginPath();
    		rotatingContext.arc(centerX, centerY, radius / 12, 2 * Math.PI, false);
    		rotatingContext.closePath();
    		rotatingContext.fillStyle = \"#444444\";
    		rotatingContext.fill();
    		rotatingContext.strokeStyle = '#000000';
    		rotatingContext.lineWidth = 5;
    		rotatingContext.stroke();
    	}
    	function drawPointer() {
    		context.beginPath();
    		context.moveTo(centerX, centerY + radius + 20);
    		context.lineTo(centerX - 10, centerY + radius + 40);
    		context.lineTo(centerX + 10, centerY + radius + 40);
    		context.closePath();
    		context.fillStyle = \"red\";
    		context.fill();
    		context.strokeStyle = \"#000000\";
    		context.stroke();
    	}
    	function drawWheel() {
    		var arcStartPoint = 0;
    		for (var game in gameList) {
    			var portion = gameList[game] / totalGameScores;
    			var arcAngle = 2 * Math.PI * portion;
    			rotatingContext.beginPath();
    			rotatingContext.moveTo(centerX, centerY);
    			rotatingContext.arc(centerX, centerY, radius, arcStartPoint, arcAngle + arcStartPoint, false);
    			rotatingContext.moveTo(centerX, centerY);
    			var textAngle = arcStartPoint;
    			arcStartPoint += arcAngle;
    			rotatingContext.closePath();
    			rotatingContext.fillStyle = nextColor();
    			rotatingContext.fill();
    			rotatingContext.lineWidth = 4;
    			rotatingContext.strokeStyle = '#000000';
    			rotatingContext.stroke();

    			rotatingContext.save();
    			rotatingContext.translate(centerX,centerY);
    			rotatingContext.rotate(Math.PI + textAngle);
    			rotatingContext.font = '35px Calibri';
    			rotatingContext.fillStyle = \"black\";
    			rotatingContext.fillText(game.toUpperCase(), 5-radius, -4);
    			rotatingContext.restore();
    			context.drawImage(rotatingCanvas, 0,0);
    		}
    	}
    	function draw() {
    		setCanvasWidth();
    		drawWheel();
    		drawWheelAxis();
    		drawPointer();
    	}
    	function roll(rollSpeed, startTime) {
    		if (stopSpinning) {
    			stopSpinning = false;
    			isSpinning = false;
    			return;
    		}
    		if (rollSpeed <= 0) {
    			isSpinning = false;
    			return;
    		} else {
    			isSpinning = true;
    		}
    		if (!startTime) {
    			startTime = new Date().getTime();
    		}
    		//var time = (new Date()).getTime() - startTime;
        var time = 6;
    		context.clearRect(0, 0, canvas.width, canvas.height);
    		rotatingContext.clearRect(0, 0, canvas.width, canvas.height);
    		rotatingContext.translate(canvas.width / 2, canvas.height / 2);
    		var rotation = rollSpeed * time;
    		rollSpeed -= rollSpeedDeceleration * time;
    		rotatingContext.rotate(rotation);
    		rotatingContext.translate(canvas.width / -2, canvas.height / -2);
    		drawWheel();
    		drawWheelAxis();
    		drawPointer();
    		requestAnimFrame(function() {
    			roll(rollSpeed, startTime);
    		});
    	}
    	draw();
    	function userInput() {
    		if (isSpinning) {
    			//stopSpinning = true;
    		} else {
    			roll(getRandomRollSpeed(), null);
    		}
      }
    	$(window).resize(draw);
    	$(\"#gameCanvas\").click(userInput);
    	$(window).keydown(userInput);
    </script>
    ";

    return $vyps_rng_wheel_html_output;
}

/*** Short Code Name for RNG Wheel ***/

add_shortcode( 'vyps-wheel', 'vyps_rng_wheel_func');

/*** PHP Functions to handle AJAX request***/

// register the ajax action for authenticated users
add_action('wp_ajax_vyps_spin_wheel_action', 'vyps_spin_wheel_action');

// handle the ajax request
function vyps_spin_wheel_action()
{

  global $wpdb; // this is how you get access to the database

  $whatever = intval( $_POST['whatever'] );

  //$whatever += 10;

  // add your logic here...
  $atts = shortcode_atts(
		array(
				'outputid' => 1,
				'outputamount' => 555,
        'refer' => 0,
				'to_user_id' => 1,
        'comment' => '',
    		'reason' => 'SPIN',
				'btn_name' => 'SPINME',
    ), $atts, 'vyps-pe' );

  $atts['to_user_id'] = get_current_user_id();

  if( $whatever != 0)
  {
    $add_result = vyps_add_func( $atts );
  }

  $rng_server_response = ( mt_rand() / mt_getrandmax() ) + (1/1000); //This is an int to get us from 0.900 to 1 the 100 should go through being rng with keeping speed reasonable.

  echo $rng_server_response;

  wp_die(); // this is required to terminate immediately and return a proper response
}
