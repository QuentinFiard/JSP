<?php
use structures\events\SemaineReveillon;

use pages\events\weekend\inscription\CancelationPage;

use structures\Option;

use structures\events\WeekendJSP;

use utilities\Server;
require_once 'classes/utilities/Server.php';
require_once 'classes/structures/events/WeekendJSP.php';
require_once 'classes/structures/events/SemaineReveillon.php';
require_once 'classes/structures/Option.php';

require_once 'classes/pages/events/weekend/inscription/CancelationPage.php';

global $user;
$event = $this->getEvent();
$hasToPay = $user->hasToPayForEvent($event);
$waitingList = $user->isOnWaitingListForEvent($event);
$options = $user->getOptionsForEvent($event);
$reservation = $user->getReservationForEvent($event);
$rentalOption = null;

$subvention_name = "none";
$subvention_value = 0;
$subvention_option = null;

$forfait_name = "default";
$forfait_value = 0;
$forfait_option = null;

$repas_name = "no";
$repas_value = 0;
$repas_option = null;

$hasSurfRental = false;

foreach($options as $option)
{
	$pos = strpos($option->getName(),'location');
	if(!($pos===false))
	{
		$rentalOption = $option;
	}
	$pos = strpos($option->getName(),'subvention');
	if(!($pos===false))
	{
		$subvention_option = $option;
		$subvention_name = $option->getName();
		$subvention_value = $option->getPriceForUser($user);
	}
	$pos = strpos($option->getName(),'forfait');
	if(!($pos===false))
	{
		$forfait_option = $option;
		$forfait_name = $option->getName();
		$forfait_value = $option->getPriceForUser($user);
	}
	$pos = strpos($option->getName(),'food');
	if(!($pos===false))
	{
		$repas_option = $option;
		$repas_name = $option->getName();
		$repas_value = $option->getPriceForUser($user);
	}
	$pos = strpos($option->getName(),'surf');
	if(!($pos===false))
	{
		$hasSurfRental = true;
	}
}

$price = $event->priceForUser($user);

?>
<div class="content" id="configurationContent">
	<input id="eventButton" type="hidden" value="buttonEvent<?php echo $event->getEventId(); ?>" />
	<img draggable="false" id="background" src="<?php echo Server::getServerRoot(); ?>img/background4.jpg" />
	<div class="wrapper">
		<div class="title">Personnalise ton inscription !</div>
		<div class="content">
			<div id="reservationSummary">
				<h2>Résumé de ton inscription</h2>
				<?php if($hasToPay) { ?>
				<input class="primaryButton" id="how_to_pay" type="button" onclick="showHowToPayBox();" value="Comment payer ?" />
				<?php } ?>
				<div class="status">
					<div class="title">Status : </div>
					<?php 
					if(!$hasToPay) { 
						if($waitingList) {
							?><div class="value registered">paiement reçu, inscrit sur liste d'attente.</div><?php
						}
						else {
							?><div class="value registered">tu es inscrit(e) à cet évênement !</div><?php
						}
					}
					else
					{
						?><div class="value waitingForPayment"><?php 
						if($waitingList) {
							?>sur liste d'attente<?php
						} else {
							?>sur liste principale<?php
						}?>, en attente de réception du paiement.</div><?php
					}?>
				</div>
				<div class="summary_wrapper">
					<div class="summary first">
						<div class="title"><?php if($hasToPay) {?>Montant à payer<?php } else {?>Montant payé<?php } ?></div>
						<?php if($hasToPay) {?><div class="ordre">Ordre : Binet JSP</div><?php } ?>
						<div class="value" id="global_price_display"><?php echo $price ?>&nbsp;€<?php if($subvention_option) {?> +&nbsp;<?php echo -$subvention_value; ?>&nbsp;€&nbsp;(non encaissé)<?php } ?></div>
						<input type="hidden" id="global_price" value="<?php echo $price ?>"/>
					</div>
					<div class="summary second">
						<div class="title">Caution</div>
						<?php if($hasToPay) {?>
						<div class="ordre">Ordre : <?php 
							if($event==WeekendJSP::shared())
							{
								?>'Madame Vacances'<?php
							} else {
								?>Belhambra<?php
							} ?>
						</div>
						<?php } ?>
						<div class="value"><?php echo $event->cautionForUser($user) ?> €</div>
					</div>
					<div class="summary third" id="selectedOptions">
						<div class="title">Options choisies</div>
						<div class="value">
							<ul>
								<?php
								foreach($options as $option)
								{
									?><li><?php echo $option->getDescription(); ?></li><?php
									$hasOption = true;
								}
								if(count($options)==0) { ?><li>Aucune</li><?php } ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<?php if($hasToPay) { ?>
			
			<form method="post" action="<?php 
							global $currentPage;
							echo $currentPage->getPath();?>">
				<input type="hidden" name="updateOptions" value="true" />
			
			<?php } ?>
				<div id="inscription_content">
					<?php if($event->isGagnantPlace($user)) { ?>
					<section id="gagnantPlace">
						<h2>Tes JSP pour 0€</h2>
						<p>Encore toutes nos félicitations pour le gain de ta place au JSP !</p>
					</section>
					<?php } ?>
					<section id="rooms">
						<h2>Chambres</h2>
						
						<?php if($user->isOnMainListForEvent($event) && $event->getAreRoomsReady()) { ?>
							<?php
								if($user->hasRoomForEvent($event)) {
									$room = $user->getRoomForEvent($event);
									$name = $room->getName();
									$hasName = ($name!=null) && !empty($name);
							?>
							<p>Tu es inscrit dans la chambre n°<?php echo $room->getRoomNumber(); ?><?php if($hasName) {?>, aka "<?php echo $name; ?>"<?php } ?><?php 
								$members = $room->getMembers();
								if(count($members)<=1) {
								?>. Tu es pour le moment le seul inscrit dans cette chambre.<?php 
								} else {
								?>, formée de <?php 
								$members = $room->getMembers();
								$first = true;
								foreach($members as $member)
								{
									if($member->getUserId()!=$user->getUserId())
									{
										if(!$first)
										{
											echo ', ';
										}
										echo $member->getFullName();
										$first=false;
									}
								}
							?> et toi-même.<?php } ?>
								<input class="primaryButton" id="roomButton" type="button" onclick="goToPage('<?php echo $this->childWithName("chambres")->getPath(); ?>');" value="Changer de chambre" />
							<?php } else { ?>
								<input class="primaryButton" id="roomButton" type="button" onclick="goToPage('<?php echo $this->childWithName("chambres")->getPath(); ?>');" value="Choisir une chambre" />
							<?php } ?>
						<?php } else if(!$user->isOnMainListForEvent($event))
						{
						?>
						<p>Tu es inscrit sur liste d'attente pour l'évènement, et ne peux donc pas encore choisir de chambres. Reviens dès que tu auras reçu l'email t'indiquant que tu es passé sur liste principale !</p> 
						<?php
						}
						else
						{ ?>
						<p>Nous n'avons pas encore reçu la liste des chambres. Nous t'enverrons un mail dès que nous l'aurons reçue pour que tu puisses choisir ta chambre. Tu peux en attendant commencer à en discuter avec tes amis, il n'y aura a priori que des chambres de <?php if($event==SemaineReveillon::shared()) { ?>3 ou 4<?php } else { ?>4 ou 6<?php } ?>.</p>
						<?php } ?>
					</section>
					
					<?php if($event == WeekendJSP::shared()){?>
					<section id="buses">
						<h2>Bus</h2>
						<?php if($user->isOnMainListForEvent($event) && $event->getAreBusesReady()) {
							if($user->hasBusForEvent($event)) {
								$bus = $user->getBusForEvent($event);
								$name = $bus->getName();
							?>
							<p>Tu es inscrit dans le bus "<?php echo $name; ?>"</p>
								<input class="primaryButton" id="busButton" type="button" onclick="goToPage('<?php echo $this->childWithName("bus")->getPath(); ?>');" value="Changer de bus" />
							<?php } else { ?>
								<input class="primaryButton" id="busButton" type="button" onclick="goToPage('<?php echo $this->childWithName("bus")->getPath(); ?>');" value="Choisir un bus" />
							<?php } ?>
						<?php } else if(!$user->isOnMainListForEvent($event))
						{
						?>
						<p>Tu es inscrit sur liste d'attente pour l'évènement, et ne peux donc pas encore choisir de bus. Reviens dès que tu auras reçu l'email t'indiquant que tu es passé sur liste principale !</p> 
						<?php
						}
						else
						{ ?>
						<p>Nous n'avons pas encore défini la répartition des bus et leurs thèmes. Nous t'enverrons un mail dès que ce sera le cas pour que tu puisses choisir ton bus.</p>
						<?php } ?>
					</section>
					<?php } ?>
					
					<?php if($event == WeekendJSP::shared() && !$event->isGagnantPlace($user) && $hasToPay && ($user->is2010() || $user->is2011())) {?>
					
					<section class="subvention">
						<h2>Subventions éventuelles</h2>
						<div class="field">
							<input id="no_subvention" type="radio" <?php if(!$subvention_option){?>checked="checked" <?php } ?>name="subvention" value="none" /><label for="no_subvention">Aucune<span class="offset"><?php if($subvention_option){?> (+<?php echo -$subvention_value; ?> €)<?php } ?></span></label>
							<input type="hidden" class="price" value="0" />
						</div>
						<?php
						
					if($user->is2010()) {
						$option1 = $event->getOptionWithName('x2010_subvention_vos_2A');
						$option2 = $event->getOptionWithName('x2010_subvention_vos_2A_et_3A');
						if($option1) {
						?>
						<div class="field">
							<input id="subvention_2A" type="radio" name="subvention" <?php if($subvention_option==$option1) { ?>checked="checked" <?php } ?>value="<?php echo htmlspecialchars($option1->getName()); ?>" /><label for="subvention_2A">Je ne suis pas allé au VOS cette année<span class="offset"><?php if($subvention_option!=$option1) { ?> (<?php 
								$offset = $option1->getPriceForUser($user)-$subvention_value;
								if($offset>=0)
								{
									echo '+';
								}
								echo $offset ?> €)<?php } ?></span></label>
							<input type="hidden" class="price" value="<?php echo $option1->getPriceForUser($user); ?>" />
						</div>
						<?php 
						}
						if($option2) {
						?>
						<div class="field">
							<input id="subvention_2A_et_3A" type="radio" name="subvention" <?php if($subvention_option==$option2) { ?>checked="checked" <?php } ?>value="<?php echo htmlspecialchars($option2->getName()); ?>" /><label for="subvention_2A_et_3A">Je ne suis allé au VOS ni cette année ni l'an dernier<span class="offset"><?php if($subvention_option!=$option2) { ?> (<?php 
								$offset = $option2->getPriceForUser($user)-$subvention_value;
								if($offset>=0)
								{
									echo '+';
								}
								echo $offset ?> €)<?php } ?></span></label>
							<input type="hidden" class="price" value="<?php echo $option2->getPriceForUser($user); ?>" />
						</div>
						<?php 
						}
					}
					if($user->is2011()) {
						$option = $event->getOptionWithName('x2011_subvention_vos');
						if($option) {
						?>
						<div class="field">
							<input id="subvention_vos_2011" type="radio" name="subvention" <?php if($subvention_option==$option) { ?>checked="checked" <?php } ?>value="<?php echo htmlspecialchars($option->getName()); ?>" /><label for="subvention_vos_2011">Je ne suis pas allé au VOS cette année<span class="offset"><?php if($subvention_option!=$option) { ?> (<?php 
								$offset = $option->getPriceForUser($user)-$subvention_value;
								if($offset>=0)
								{
									echo '+';
								}
								echo $offset ?> €)<?php } ?></span></label>
							<input type="hidden" class="price" value="<?php echo $option->getPriceForUser($user); ?>" />
						</div>
						<?php 
						}
					}
					?>
						<input id="old_subvention_name" type="hidden" value="<?php echo htmlspecialchars($subvention_name); ?>" />
						<input id="old_subvention" type="hidden" value="<?php echo $subvention_value; ?>" />
							
					</section>
					<?php } ?>
					<section>
						<h2>Location</h2>
						
						<p>Si tu n'as pas ton matériel, tu peux choisir entre plusieurs formules pour la location. Indique si tu souhaites louer un surf ou des skis, s'il ne te faut que des skis/chaussures, ou s'il te faut les deux, et choisit le type de matériel que tu veux (éco = pas cher / sensation = gros gros mythe mais €€€).</p>
						<?php if($hasToPay) { ?>
						<div class="column left">
							<div class="title">Pas de location</div>
							<input class="price" type="hidden" value="0" />
							<input type="radio" name="location" value="no"<?php if(!$rentalOption){?> checked="checked"<?php }?>/>
						</div>
						<div class="column right">
							<div class="title">Je veux louer du matériel</div>
							<section id="rentalType">
								<h2>Type</h2>
								<div class="fieldbis">
									<div class="name">Ski</div>
									<input type="radio" name="rentalType" value="ski"<?php if($rentalOption && !$hasSurfRental) {?> checked="checked"<?php }?> />
								</div>
								<div class="fieldbis">
									<div class="name">Surf</div>
									<input type="radio" name="rentalType" value="surf"<?php if($rentalOption && $hasSurfRental) {?> checked="checked"<?php }?> />
								</div>
							</section>
							<table id="ski"<?php if(!$rentalOption || $hasSurfRental) {?> style="display:none"<?php } ?>>
								<tr>
									<th></th>
									<th>Éco</th>
									<th>Découverte</th>
									<th>Sensation</th>
								</tr>
								<?php 
									$choices = array(
										'Pack' => array('location_pack_ski_eco','location_pack_ski_decouverte','location_pack_ski_sensation'),
										'Ski seuls' => array('location_ski_eco','location_ski_decouverte','location_ski_sensation'),
										'Chaussures seules' => array('location_chaussures_ski_eco','location_chaussures_ski_decouverte','location_chaussures_ski_sensation'),
									);
									foreach($choices as $choice => $values)
									{
										?><tr>
										<td><?php echo $choice; ?></td><?php 
										foreach($values as $value)
										{
											if($value)
											{
												$option = $event->getOptionWithName($value);
												?>
											<td>
												<div class="price"><?php echo $option->getPriceForUser($user); ?> €</div>
												<input class="price" type="hidden" value="<?php echo $option->getPriceForUser($user); ?>" />
												<input type="radio" name="location" value="<?php echo htmlspecialchars($value); ?>"<?php if($rentalOption && $rentalOption->getName() == $value) {?> checked="checked"<?php }?> />
											</td><?php
											}
											else
											{
												?><td></td><?php
											}
										}
									?></tr><?php
									}
								?>
							</table>
							<table id="surf"<?php if(!$rentalOption || !$hasSurfRental) {?> style="display:none"<?php } ?>>
								<tr>
									<th></th>
									<th>Éco</th>
									<th>Découverte</th>
									<th>Sensation</th>
								</tr>
								<?php 
									$choices = array(
										'Pack' => array(null,'location_pack_surf_decouverte','location_pack_surf_sensation'),
										'Surf seul' => array(null,'location_surf_decouverte','location_surf_sensation'),
										'Chaussures seules' => array('location_chaussures_surf_eco','location_chaussures_surf_decouverte','location_chaussures_surf_sensation'),
									);
									foreach($choices as $choice => $values)
									{
									?><tr>
										<td><?php echo $choice; ?></td><?php 
										foreach($values as $value)
										{
											if($value)
											{
												$option = $event->getOptionWithName($value);
												?>
											<td>
												<div class="price"><?php echo $option->getPriceForUser($user); ?> €</div>
												<input class="price" type="hidden" value="<?php echo $option->getPriceForUser($user); ?>" />
												<input type="radio" name="location" value="<?php echo htmlspecialchars($value); ?>"<?php if($rentalOption && $rentalOption->getName() == $value) {?> checked="checked"<?php }?> />
											</td><?php
											}
											else
											{
												?><td></td><?php
											}
										}
									?></tr><?php
									}
								?>
							</table>
						</div>
					</section>
					
					<?php if($hasToPay && $event == SemaineReveillon::shared()){?>
					<section id="forfaits">
						<h2>Forfaits</h2>
						<div class="field">
							<input id="forfait_standard" type="radio" <?php if(!$forfait_option){?>checked="checked" <?php } ?>name="forfait" value="default" /><label for="forfait_standard">Forfait La Plagne 6 jours<span class="offset"><?php if($forfait_option) { ?> (<?php 
								$offset = -$forfait_value;
								if($offset>=0)
								{
									echo '+';
								}
								echo $offset ?> €)<?php } ?></span></label>
							<input type="hidden" class="price" value="0" />
						</div>
						<?php $option = $event->getOptionWithName('forfait_paradiski'); ?>
						<div class="field">
							<input id="forfait_etendu" type="radio" <?php if($forfait_option==$option){?>checked="checked" <?php } ?>name="forfait" value="<?php echo htmlspecialchars($option->getName()) ?>" /><label for="forfait_etendu">Forfait 6 jours Paradiski (La Plagne + Les Arcs)<span class="offset"><?php if($forfait_option!=$option) { ?> (<?php 
								$offset = $option->getPriceForUser($user)-$forfait_value;
								if($offset>=0)
								{
									echo '+';
								}
								echo $offset ?> €)<?php } ?></span></label>
							<input type="hidden" class="price" value="<?php echo $option->getPriceForUser($user); ?>" />
						</div>
						<?php $option = $event->getOptionWithName('no_forfait'); ?>
						<div class="field">
							<input id="forfait_etendu" type="radio" <?php if($forfait_option==$option){?>checked="checked" <?php } ?>name="forfait" value="<?php echo htmlspecialchars($option->getName()) ?>" /><label for="forfait_etendu">Pas de forfait<span class="offset"><?php if($forfait_option!=$option) { ?> (<?php 
								$offset = $option->getPriceForUser($user)-$forfait_value;
								if($offset>=0)
								{
									echo '+';
								}
								echo $offset ?> €)<?php } ?></span></label>
							<input type="hidden" class="price" value="<?php echo $option->getPriceForUser($user); ?>" />
						</div>
					</section>
					
					<section id="repas">
						<h2>Repas</h2>
						<div class="field">
							<input id="pas_de_repas" type="radio" <?php if(!$repas_option){?>checked="checked" <?php } ?>name="repas" value="no" /><label for="pas_de_repas">Pas de repas<span class="offset"><?php if($repas_option) { ?> (<?php 
								$offset = -$repas_value;
								if($offset>=0)
								{
									echo '+';
								}
								echo $offset ?> €)<?php } ?></span></label>
							<input type="hidden" class="price" value="0" />
						</div>
						<?php $option = $event->getOptionWithName('food_pack'); ?>
						<div class="field">
							<input id="food_pack" type="radio" <?php if($repas_option==$option){?>checked="checked" <?php } ?>name="repas" value="<?php echo htmlspecialchars($option->getName()) ?>" /><label for="food_pack">Paniers repas à midi<span class="offset"><?php if($repas_option!=$option) { ?> (<?php 
								$offset = $option->getPriceForUser($user)-$repas_value;
								if($offset>=0)
								{
									echo '+';
								}
								echo $offset ?> €)<?php } ?></span></label>
							<input type="hidden" class="price" value="<?php echo $option->getPriceForUser($user); ?>" />
						</div>
						<?php $option = $event->getOptionWithName('food_pack_sans_porc'); ?>
						<div class="field">
							<input id="food_pack_sans_porc" type="radio" <?php if($repas_option==$option){?>checked="checked" <?php } ?>name="repas" value="<?php echo htmlspecialchars($option->getName()) ?>" /><label for="food_pack_sans_porc">Paniers repas sans porc à midi<span class="offset"><?php if($repas_option!=$option) { ?> (<?php 
								$offset = $option->getPriceForUser($user)-$forfait_value;
								if($offset>=0)
								{
									echo '+';
								}
								echo $offset ?> €)<?php } ?></span></label>
							<input type="hidden" class="price" value="<?php echo $option->getPriceForUser($user); ?>" />
						</div>
					</section>
					<?php } ?>
					
				</div>
				<?php if(!isset($reservation['cashed'])) { ?>
				<input id="cancelButton" class="warningButton" type="button" onclick="showCancelReservationBox('<?php echo $this->getParent()->childWithName('cancelation')->getPath(); ?>');" value="Annuler l'inscription" />
				<?php } ?>
				<input id="saveButton" class="primaryButton" type="submit" value="Enregistrer les modifications" />
				</form>
			<?php } else {?>
				</div>
			<p id="afterPaymentLabel">Tu as déjà payé, tu ne peux donc plus modifier ton inscription. Si tu souhaites la modifier, envoie nous un mail à l'adresse <a href="mailto:jsp@binets.polytechnique.fr?Subject=<?php echo urlencode("[JSP] Demande de modification d'inscription"); ?>">jsp@binets.polytechnique.fr</a>.</p>
			
			<?php if(!isset($reservation['cashed'])) { ?>
			<input id="cancelButton" class="warningButton" type="button" onclick="showCancelReservationBox('<?php echo $this->getParent()->childWithName('cancelation')->getPath(); ?>');" value="Annuler l'inscription" />
			<?php }?>
			
			<?php } ?>
		</div>
	</div>
</div>