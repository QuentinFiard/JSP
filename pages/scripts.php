<?php
use pages\scripts\CurrentScriptPage;

use utilities\Server;

require_once('classes/utilities/Server.php');
require_once 'classes/pages/connexion/ExterieursPage.php';
require_once 'classes/pages/admin/rooms/RoomsReveillonPage.php';
require_once 'classes/pages/admin/rooms/RoomsWeekendPage.php';
require_once 'classes/pages/admin/eventoptions/ReveillonPage.php';
require_once 'classes/pages/admin/eventoptions/WeekendPage.php';
require_once 'classes/pages/admin/UserPaymentPage.php';

use \pages\connexion\ExterieursPage;

require_once 'classes/utilities/Server.php';
require_once 'classes/pages/scripts/CurrentScriptPage.php';

?>

<div class="content admin" id="adminContent">
	<a href="<?php echo Server::getServerRoot().substr(CurrentScriptPage::getPage()->getPath(),1);?>">Current script</a>
</div>