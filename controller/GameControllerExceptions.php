<?php
	# exception class for user command syntax error
	class SyntaxException extends Exception {
		const private $command;
		const private $configs;

		# mandatory message and (invalid) command
	    public function __construct($command, $code = 0, Exception $previous = null) {
	    	$configs = include('../resources/controller-config.php');
	   		parent::__construct($configs['syntaxExceptionMessage'], $code, $previous);
	   		$this->command = $command;
	    }

	    # getter for command
	    public function getCommand() {
	    	return $command;
	    }

	    # format an user-friendly error 
	    public function __toString() {
	    	$error = $message . '<br>' . "The command was: " . $command;
	    	return $error;
	    }
	}

?>