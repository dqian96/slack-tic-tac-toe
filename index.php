<?php

	require_once('controller/GameController.php'); 

	# load configuration settings from file
	$configs = include('resources/config.php');


	# extract values from slash command
	$token = $_POST['token'];

	$team_id = $_POST['team_id'];
	$channel_id = $_POST['channel_id'];

	$user_id = $_POST['user_id'];
	$user_name = $_POST['user_name'];

	$text = $_POST['text'];


	
	if ($token != $configs['token']) { 
		# exit if request token does not match the configured token
		$reply = $configs['tokenMismatchMessage'];
	} else {
		# create a new game controller and pass the user command to it
		$gameController = new GameController;
		try {
			$reply = $gameController->command($text);
		} catch(Exception $e) {
			$reply = $e->__toString();
		}
	}

	# relay response
	echo $reply;
	exit();

?>



