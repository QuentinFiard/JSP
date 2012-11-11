<?php

namespace utilities;

class Server {
	
	static public function getServerRoot(){
		return '/jsp/';
	}
	
	static public function getServerFullURL(){
		return 'http://'.$_SERVER['HTTP_HOST'].Server::getServerRoot();
	}
	
}

?>