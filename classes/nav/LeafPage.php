<?php

namespace nav;

require_once 'classes/nav/Page.php';

class LeafPage extends Page {
	public function isLeaf() {
		return true;
	}
}

?>