<?php
	require_once('app/model/Game.php');
	require_once('app/controller/GameController.php'); 

	# load configuration settings from file
	$configs = include('server-config.php');

	if (!(isset($_POST['token']) and isset($_POST['user_name']) and isset($_POST['text']) and isset($_POST['response_url']))) {
		# invalid form data
		$reply = $configs['invalidFormMessage'];
		echo $reply;
	} else if ($_POST['token'] != $configs['token']) {
		# token mismatch
		$reply = $configs['tokenMismatchMessage'];
		echo $reply;
	} else {
		# load previous game model state information
		$state = file_get_contents('app/resources/' + configs['gameSaveName']);
		if (!$state) {
			# create a new game model if no state exists
			$game = new Game;
		} else {
			# load state from file
			$game = unserialize($state);
		}

		# create a new game controller and pass the game model to it
		$gameController = new GameController($game);

		try {
			# pass the user command to the controller
			$reply = $gameController->command($text);

			# save game state
			$state = serialize($game);
			file_put_contents('app/resources/' + configs['gameSaveName'], $state);
		} catch(Exception $e) {
			$reply = array($e->__toString();
		}

		$responseJSON = json_encode($reply);
		$userAgent = 'Tic-Tac-Toe/1.0';

		$ch = curl_init($_POST['response_url']);
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $responseJSON);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($responseJSON))
		);
		curl_exec($ch);
	}
?>



