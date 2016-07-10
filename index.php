<?php

	$configs = include('config.php');

	print_r($_GET);
	print_r($_POST);




	// extract values from slash command
	$token = $_POST['token'];

	$team_id = $_POST['team_id'];
	$channel_id = $_POST['channel_id'];

	$user_id = $_POST['user_id'];
	$user_name = $_POST['user_name'];

	$text = $_POST['text'];

	echo $configs['token'];





