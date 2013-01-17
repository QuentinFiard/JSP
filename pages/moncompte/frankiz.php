<?php
use pages\EventsPage;

use utilities\Server;

require_once('classes/utilities/Server.php');
require_once('classes/pages/EventsPage.php');

global $currentPage;
global $user;

$nickname = $user->getNickname();
$hasNickName = ($nickname!=null && !empty($nickname));
?>
<div class="content frankiz<?php if(!$hasNickName){?> nonickname<?php } ?>" id="monCompteContent">
	<div id="wrapper">
		<div class="menu">
			<div class="title">Mon compte</div>
			<div class="content">
				<section id="personalData">
					<div class="title">Données personnelles</div>
					<section id="frankizData">
						<div class="title">Frankiz</div>
						<div class="content">
							<div class="title">Les informations suivantes nous ont été communiquées par l'intermédiaire de Frankiz en accord avec le binet Réseau. Rends toi sur <a href="http://www.frankiz.net">le site de Frankiz</a> pour les modifier.</div>
							<div class="fieldset">
								<div class="field">
									<div class="name">Nom</div>
									<div class="value"><?php echo $user->getFullName(); ?></div>
								</div>
								<?php if($hasNickName) {?>
								<div class="field">
									<div class="name">Surnom</div>
									<div class="value"><?php echo $nickname; ?></div>
								</div>
								<?php } ?>
								<div class="field">
									<div class="name">Adresse mail</div>
									<div class="value"><?php echo $user->getEmail(); ?></div>
								</div>
								<div class="field">
									<div class="name">Promo</div>
									<div class="value"><?php echo ucfirst($user->getClass()); ?></div>
								</div>
							</div>
						</div>
					</section>
					<section id="otherData">
						<div class="title">Autres données</div>
						<div class="content">
							<div class="title">Ces informations sont nécessaires pour louer du matériel pendant les évênements.</div>
							<div class="fieldset">
								<form class="field" action="<?php echo Server::getServerRoot().substr($currentPage->getPath(), 1); ?>">
									<input type="hidden" name="updateValue" value="true" />
									<div class="name">Masse</div>
									<input class="value" type="number" name="weight" value="<?php echo htmlspecialchars($user->getWeight()); ?>" />
									<input class="old_value" type="hidden" name="old_weight" value="<?php echo htmlspecialchars($user->getWeight()); ?>" />
									<div class="unit">kg</div>
									<input type="submit" class="button" value="Modifier">
								</form>
								<form class="field" action="<?php echo Server::getServerRoot().substr($currentPage->getPath(), 1); ?>">
									<input type="hidden" name="updateValue" value="true" />
									<div class="name">Taille</div>
									<input class="value" type="number" name="height" value="<?php echo htmlspecialchars(100*$user->getHeight()); ?>" />
									<input class="old_value" type="hidden" name="old_height" value="<?php echo htmlspecialchars(100*$user->getHeight()); ?>" />
									<div class="unit">cm</div>
									<input type="submit" class="button" value="Modifier">
								</form>
								<form class="field" action="<?php echo Server::getServerRoot().substr($currentPage->getPath(), 1); ?>">
									<input type="hidden" name="updateValue" value="true" />
									<div class="name">Pointure</div>
									<input class="value" type="number" name="size" value="<?php echo htmlspecialchars($user->getSize()); ?>" />
									<input class="old_value" type="hidden" name="old_size" value="<?php echo htmlspecialchars($user->getSize()); ?>" />
									<div class="unit">&nbsp;</div>
									<input type="submit" class="button" value="Modifier">
								</form>
							</div>
						</div>
					</section>
				</section>
				<section id="inscriptions">
					<div class="title">Inscriptions</div>
					<input class="primaryButton" type="submit" onclick="goToPage('<?php echo EventsPage::getPage()->getPath(); ?>');" value="Voir l'état de tes inscriptions" />
				</section>
			</div>
		</div>
	</div>
</div>