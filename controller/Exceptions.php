<?php
	# exception class for user command syntax error
	class SyntaxError extends Exception {
		const private $command;

		# mandatory message and (invalid) command
	    public function __construct($message, $command, $code = 0, Exception $previous = null) {
	   		parent::__construct($message, $code, $previous);
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