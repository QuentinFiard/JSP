<?php 

use utilities\Server;

require_once 'classes/structures/Session.php';
require_once 'classes/utilities/Server.php';

use \structures\Session;

Session::unsetKey('user');

header("Location: ".Server::getServerRoot());