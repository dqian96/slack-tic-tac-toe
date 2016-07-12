<?php
	require_once('app/model/Game.php');
	require_once('app/controller/GameController.php'); 

	# load configuration settings from file
	$configs = include('server-config.php');

	if (!(isset($_POST['token']) or $_POST['token'] != $configs['token']) {
		# token does not exist or mismatch -- wrong team configuration or unknown client
		$response = $configs['tokenMismatchMessage'];
		echo $response;
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

		# pass the user command to the controller
		$response = $gameController->command($_POST['text'], $_POST['user_name']);

		# save game state
		$state = serialize($game);
		file_put_contents('app/resources/' + configs['gameSaveName'], $state);
		
		$responseJSON = json_encode($response);
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



