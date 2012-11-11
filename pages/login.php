<?

use utilities\Server;

require_once('classes/process/Frankiz.php');
require_once 'classes/utilities/Server.php';
use \process\Frankiz;

if(Frankiz::hasFrankizResponse())
{
	Frankiz::checkResponseValidity();
	
	if(isset($_GET['location']) && !empty($_GET['location']) && $_GET['location']!=Server::getServerFullURL())
	{
		header("Location: ".$_GET['location'].'login?'.http_build_query($_GET));
		exit();
	}
	
	// Processing response
	Frankiz::processResponse();

	header("Location: ".Server::getServerRoot());
	exit();
}
else
{
	Frankiz::startFrankizAuth();
}