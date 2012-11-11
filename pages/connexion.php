<?php
use utilities\Server;

require_once('classes/utilities/Server.php');
require_once 'classes/pages/connexion/ExterieursPage.php';

use \pages\connexion\ExterieursPage;
require_once 'classes/utilities/Server.php';
?>
<div class="content" id="connectionContent">
	<img draggable="false" id="background" src="<?php echo Server::getServerRoot(); ?>img/background2.jpg" />
	
	<div id="choices_wrapper">
		<div id="choices">
			<div class="choice" id="frankizConnection" onclick="window.location.href = '<?php echo Server::getServerRoot(); ?>login'">
				<div class="overlay"></div>
				<div class="content">
					<div class="title">Connexion par Frankiz</div>
					<div class="subtitle">(X, ENSTA, Sup'Optique)</div>
					<img class="logo" src="img/frankiz.png" />
				</div>
			</div>
			<div class="choice" id="externalConnection" onclick="goToPage('<?php echo ExterieursPage::getPage()->getPath(); ?>');">
				<div class="overlay"></div>
				<div class="content">
					<div class="title">Connexion (extérieurs)</div>
					<div class="subtitle">&nbsp;</div>
					<img class="logo" src="img/exterieurs.png" />
				</div>
			</div>
		</div>
	</div>
</div>