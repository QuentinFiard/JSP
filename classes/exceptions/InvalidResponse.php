<?php

namespace exceptions;

class InvalidResponse extends \Exception {
	
	public function __construct($message = null, $code = null, $previous = null) {
		parent::__construct($message,$code,$previous);
	}
	
}

?>