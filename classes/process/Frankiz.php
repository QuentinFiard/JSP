<?php

namespace process;

require_once 'classes/utilities/Server.php';

use utilities\Server;

use structures\FrankizUser;
use structures\Session;

use exceptions\InvalidContactInfos;

use utilities\FormValidator;

use exceptions\InvalidResponse;

require_once ('classes/exceptions/InvalidResponse.php');
require_once ('classes/exceptions/InvalidContactInfos.php');
require_once('classes/utilities/FormValidator.php');
require_once('classes/structures/FrankizUser.php');
require_once('classes/structures/Session.php');

require_once 'classes/database/Database.php';

require_once 'classes/SensitiveData.php';

use \database\Database;

use \exceptions;
use \utilities;

class Frankiz {
	static private $key; // Clé nécessaire à l'authentification interne de Frankiz, communiquée par le BR
	/**
	 * url de la page de login, doit correspondre *exactement* à celle entrée dans
	 * la base de données de Frankiz (définie lors de l'inscription)
	 */
	static private $site = "http://jsp.binets.fr/login";

	static public function init()
	{
	    self::$key = \SensitiveData::$frankizKey;
	}

	static public function hasFrankizResponse()
	{
		return isset($_GET['response']);
	}

	static public function startFrankizAuth()
	{
		// Copyright BR 2010
		/**
		 * Prendre le timestamp permet d'éviter le rejeu de la requête
		 */
		$timestamp = time();
		/**
		 * Nature de la requête.
		 * Fkz renverra ici à la fois les noms de la personne mais aussi ses droits dans différents groupes.
		 * Il faut cependant que le site ait les droits sur les informations en question (à définir lors de son inscription).
		 */
		$request = json_encode(array('names', 'rights', 'email', 'promo', 'sport'));

		$hash = md5($timestamp . self::$site . self::$key . $request);

		$remote  = 'https://www.frankiz.net/remote?timestamp=' . $timestamp .
		'&site=' . self::$site .
		'&location=' . 'http://'.$_SERVER['HTTP_HOST'].Server::getServerRoot() .
		'&hash=' . $hash .
		'&request=' . $request;
		header("Location:" . $remote);
		exit();
	}

	static public function checkResponseValidity()
	{
		if(!isset($_GET['timestamp']) || !isset($_GET['response']) || !isset($_GET['hash']))
		{
			throw new InvalidResponse("La réponse de Frankiz est incomplète");
		}

		$timestamp = $_GET['timestamp'];
		$hash = $_GET['hash'];
		$response = $_GET['response'];

		// Frankiz security protocol
		if(abs($timestamp - time()) > 600)
		{
			throw new InvalidResponse("Frankiz n'a pas répondu dans un délai raisonnable, la requête a été annulée.");
		}
		if(md5($timestamp . self::$key . $response) != $hash)
		{
			throw new InvalidResponse("Votre compte Frankiz semble victime d'une attaque. Merci de contacter le BR pour plus d'informations.");
		}
	}

	static public function processResponse()
	{
		// Copyright BR 2010 & Quentin Fiard
		//http://jsp.binets.fr/login?location=&timestamp=1349803209&response={%22uid%22:%2212402%22,%22hruid%22:%22quentin.fiard%22,%22firstname%22:%22Quentin%22,%22lastname%22:%22Fiard%22,%22nickname%22:%22Fifi%22,%22email%22:%22quentin.fiard@polytechnique.edu%22,%22rights%22:{%22jsp%22:[%22admin%22,%22member%22,%22restricted%22,%22everybody%22]}}&hash=e4e69df94595c46943495d51a49f4bb1
		// Read request
		self::checkResponseValidity();

		$response = $_GET['response'];
		$response = json_decode($response, true);

		$admin = false;
		$member = false;

		if(array_key_exists('rights', $response))
		{
			$rights = $response['rights'];
			if(array_key_exists('jsp', $rights))
			{
				$rights_jsp = $rights['jsp'];
				foreach($rights_jsp as $value)
				{
					if($value == "admin")
					{
						$admin = true;
					}
					if($value == "member")
					{
						$member = true;
					}
				}
			}
		}

		$securityLevel = SecurityLevel::$Registered;

		if($member)
		{
			$securityLevel = SecurityLevel::$Member;
		}
		if($admin)
		{
			$securityLevel = SecurityLevel::$Admin;
		}


		$response['securityLevel'] = $securityLevel;

		$fields = array('uid' => 'number',
						'hruid' => 'hruid',
						'firstname' => 'name',
						'lastname' => 'name',
						'nickname' => 'name',
						'email' => 'email',
						'securityLevel' => 'number',
						'promo' => 'number');

		/*$validator = new FormValidator($fields,array_keys($fields));

		if(!$validator->validate($response))
		{
			throw new InvalidContactInfos();
		}*/

		$user = Database::shared()->getFrankizUserWithUID($response['uid']);

		if(isset($user))
		{
			$user->updateWithData($response);
		}
		else
		{
			$user = new FrankizUser($response);
		}

		$user->save();

		Session::setValueForKey('userId', $user->getUserId());
	}
}

Frankiz::init();

?>