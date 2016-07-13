<?php
	require_once(__DIR__ . '/app/model/Game.php');
	require_once(__DIR__ . '/app/controller/GameController.php'); 

	# load configuration settings from file
	$configs = include('server-config.php');
	if (!isset($_POST['token']) || $_POST['token'] != $configs['token']) {
		# token does not exist or mismatch -- wrong team configuration or unknown client
		$response = $configs['tokenMismatchMessage'];
		echo $response;
	} else {
		$gameStateForChannelFileName = $_POST['channel_id'] . '-' . $configs['gameSaveName'];
		# load previous game model state information
		if (file_exists('app/data/' . $gameStateForChannelFileName)) {
			$state = file_get_contents('app/data/' . $gameStateForChannelFileName);
			# load state from file
			$game = unserialize($state);
		} else {
			# create a new game model if no state exists
			$game = new Game;
		}

		# create a new game controller and pass the game model to it
		$gameController = new GameController($game);

		# pass the username and user command to the controller
		$response = $gameController->command($_POST['text'], $_POST['user_name']);

		# save game state
		$state = serialize($game);
		file_put_contents('app/data/' . $gameStateForChannelFileName, $state);
		
		# send response using cURL
		$responseJSON = json_encode($response, JSON_UNESCAPED_SLASHES);

		$userAgent = 'Tic-Tac-Toe/1.0';

		$ch = curl_init($_POST['response_url']);
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $responseJSON);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($responseJSON)),
			'Status: 200'
		);
		curl_exec($ch);
		curl_close($ch);
	}
?>