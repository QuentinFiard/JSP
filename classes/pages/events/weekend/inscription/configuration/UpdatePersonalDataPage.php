<?php

namespace pages\events\weekend\inscription\configuration;

require_once 'classes/pages/events/UpdatePersonalDataPage.php';

class UpdatePersonalDataPage extends \pages\events\UpdatePersonalDataPage {
	private static $page = null;

	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new UpdatePersonalDataPage();
		}
		return self::$page;
	}

}

?>