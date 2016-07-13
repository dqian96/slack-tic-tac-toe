<?php
	# exception class for user command syntax error
	class SyntaxException extends Exception {
		private $command;

		# mandatory message and (invalid) command
	    public function __construct($command, $code = 0, Exception $previous = null) {
	    	$configs = include($_SERVER['DOCUMENT_ROOT'] . '/slack-tic-tac-toe/app/resources/controller-config.php');
	   		parent::__construct($configs['syntaxExceptionMessage'], $code, $previous);
	   		$this->command = $command;
	    }

	    # getter for command
	    public function getCommand() {
	    	return $this->command;
	    }

	    # format an user-friendly error 
	    public function __toString() {
	    	$error = $this->message . '\nThe command was: ' . $this->command;
	    	return $error;
	    }
	}
?>