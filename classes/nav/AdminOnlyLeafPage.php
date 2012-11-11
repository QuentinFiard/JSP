<?php

namespace nav;

require_once 'classes/nav/AdminOnlyPage.php';

class RegisteredOnlyLeafPage extends AdminOnlyPage {
	public function isLeaf() {
		return true;
	}
}

?>