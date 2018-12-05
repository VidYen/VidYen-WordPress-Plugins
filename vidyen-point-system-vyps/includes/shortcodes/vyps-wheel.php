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
  //NOTE: Doing to use "" instead of '' as well... As put variables in the code directly.
  $vyps_rng_wheel_html_output = "
    <style type=\"text/css\">
    body {
    	background: black;
    }
    #gameCanvas {
    }
    </style>

    <script src=\"jquery-1.8.3.min.js\"></script>

    <canvas width=\"600\" height=\"400\" id=\"gameCanvas\">
    </canvas>

    <script type=\"text/javascript\">
    	window.requestAnimFrame = (function(callback) {
            return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame ||
            function(callback) {
              window.setTimeout(callback, 1000 / 60);
            };
          })();
    	var gameList = {
    		\"0\" : 25.0,
    		\"50\" : 25.0,
    		\"100\" : 25.0,
    		\"200\" : 25.0,
    	}
    	var totalGameScores = 0;
    	for (var game in gameList) {
    		totalGameScores += gameList[game];
    	}
    	var sectorColors = [
    		\"#00CC00\",
    		\"#FF0000\",
    		\"#FF7400\",
    		\"#009999\"
    	]
    	function getRandomRollSpeed() {
    		return 1/1000 + Math.random() * 1 / 1000;
    	}

    	var rollSpeedDeceleration = 1/100000000;
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
    		canvas.width = window.innerWidth - 20;
    		canvas.height = window.innerHeight - 20;
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
    		var time = (new Date()).getTime() - startTime;
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
    			stopSpinning = true;
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
