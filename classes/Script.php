<?php

use utilities\Server;

use structures\User;

require_once 'classes/structures/User.php';
require_once 'classes/utilities/Server.php';

abstract class Script
{
    public function __construct()
    {
        header('Content-Type: text/plain; charset=UTF-8');
        $user = User::currentUser();
        if(!isset($user) || !$user->isAdmin())
        {
            header('Location: '.Server::getServerRoot().'/connexion');
            exit();
        }
    }
    
    abstract public function execute();
}

?>