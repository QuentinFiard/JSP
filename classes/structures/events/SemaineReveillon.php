<?php

namespace structures\events;

require_once ('classes/structures/Event.php');
require_once ('classes/database/Database.php');

use structures\Event;
use \database\Database;

class SemaineReveillon extends Event {
	static private $shared = null;
	
	protected function __construct()
	{
		parent::__construct(1);
	}
	
	static public function shared()
	{
		if(self::$shared==null)
		{
			self::$shared = new SemaineReveillon();
		}
		return self::$shared;
	}
	
	public function getPage()
	{
		return \pages\events\ReveillonPage::getPage();
	}
	
	public function getNameWithPrefixA() {
		return 'à la semaine du réveillon';
	}
	
	public function getNameWithPrefixPour() {
		return 'pour la semaine du réveillon';
	}

}

?>