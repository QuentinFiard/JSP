<?php

namespace nav;

require_once 'classes/nav/RegisteredOnlyPage.php';

class RegisteredOnlyLeafPage extends RegisteredOnlyPage {
	public function isLeaf() {
		return true;
	}
}

?>