<?php
use pages\events\reveillon\inscription\CancelationPage;

use structures\Option;

use structures\events\SemaineReveillon;

use utilities\Server;
require_once 'classes/utilities/Server.php';
require_once 'classes/structures/events/SemaineReveillon.php';
require_once 'classes/structures/Option.php';

require_once 'classes/pages/events/reveillon/inscription/CancelationPage.php';

global $user;
$event = $this->getEvent();
$hasToPay = $user->hasToPayForEvent($event);
$waitingList = $user->isOnWaitingListForEvent($event);
$options = $user->getOptionsForEvent($event);
$hasRental = (count($options)>0);
$currentOption = null;
if($hasRental)
{
	$currentOption = $options[0];
}
?>
<div class="content" id="configurationContent">
	<input id="eventButton" type="hidden" value="buttonEvent1" />
	<img draggable="false" id="background" src="<?php echo Server::getServerRoot(); ?>img/background4.jpg" />
	<div class="wrapper">
		<div class="title">Personnalise ton inscription !</div>
		<div class="content">
			<div id="reservationSummary">
				<h2 style="float:left;">Résumé de ton inscription</h2>
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
						<div class="title"><?php if($user->hasToPayForEvent($event)) {?>Prix à payer<?php } else {?>Montant payé<?php } ?></div>
						<div class="value" id="global_price_display"><?php echo $event->priceForUser($user) ?> €</div>
						<input type="hidden" id="global_price" value="<?php echo $event->priceForUser($user) ?>"/>
					</div>
					<div class="summary second">
						<div class="title">Caution</div>
						<div class="value"><?php echo $event->cautionForUser($user) ?> €</div>
					</div>
					<div class="summary third" id="selectedOptions">
						<div class="title">Options choisies</div>
						<div class="value">
							<ul>
								<?php 
								$hasOption = false;
								if($currentOption) {?>
								<li><?php echo $currentOption->getDescription(); ?></li>
								<?php 
									$hasOption = true;
								} ?>
								<?php if(!$hasOption) { ?><li>Aucune</li><?php } ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
			
			<h2>Chambre</h2>
			<?php if($event->getAreRoomsReady()) { ?>
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
					<input class="primaryButton" type="button" onclick="" value="Changer de chambre" />
				<?php } else { ?>
					<input class="primaryButton" type="button" onclick="" value="Choisir une chambre" />
				<?php } ?>
			<?php } else { ?>
			<p>Nous n'avons pas encore reçu la liste des chambres. Nous t'enverrons un mail dès que nous l'aurons reçue pour que tu puisses choisir ta chambre. Tu peux en attendant commencer à en discuter avec tes amis, il n'y aura a priori que des chambres de 4 ou de 6.</p>
			<?php } ?>
			
			<h2>Location</h2>
			
			<p>Si tu n'as pas ton matériel, tu peux choisir entre plusieurs formules pour la location. Indique si tu ne souhaites louer que des skis/chaussures, ou s'il te faut les deux, et choisit le type de matériel que tu veux (éco = pas cher / sensation = gros gros mythe mais €€€).</p>
			<?php if($hasToPay) { ?>
			<form method="post" action="<?php 
							global $currentPage;
							echo $currentPage->getPath();?>">
				<input type="hidden" name="updateLocationChoice" value="true" />
				<div class="column left">
					<div class="title">Pas de location</div>
					<input class="price" type="hidden" value="0" />
					<input type="radio" name="location" value="no"<?php if(!$hasRental){?> checked="checked"<?php }?>/>
				</div>
				<div class="column right">
					<div class="title">Je veux louer du matériel</div>
					<table>
						<tr>
							<th></th>
							<th>Éco</th>
							<th>Découverte</th>
							<th>Sensation</th>
						</tr>
						<?php 
							$choices = array(
								'Pack' => array('location_pack_eco','location_pack_decouverte','location_pack_sensation'),
								'Ski seuls' => array('location_ski_eco','location_ski_decouverte','location_ski_sensation'),
								'Chaussures seules' => array('location_chaussures_eco','location_chaussures_decouverte','location_chaussures_sensation'),
							);
							foreach($choices as $choice => $values)
							{
							?><tr>
								<td><?php echo $choice; ?></td><?php 
								foreach($values as $value)
								{
									$option = $event->getOptionWithName($value);
									?>
								<td>
									<div class="price"><?php echo $option->getPriceForUser($user); ?> €</div>
									<input class="price" type="hidden" value="<?php echo $option->getPriceForUser($user); ?>" />
									<input type="radio" name="location" value="<?php echo htmlspecialchars($value); ?>"<?php if($currentOption && $currentOption->getName() == $value) {?> checked="checked"<?php }?> />
								</td><?php
								}
							?></tr><?php
							}
						?>
					</table>
				</div>
				<input id="cancelButton" class="warningButton" type="button" onclick="showCancelReservationBox('<?php echo CancelationPage::getPage()->getPath(); ?>');" value="Annuler l'inscription" />
				<input id="saveButton" class="primaryButton" type="submit" value="Enregistrer les modifications" />
			</form>
			<?php } else {?>
			<p id="afterPaymentLabel">Tu as déjà payé, tu ne peux donc plus modifier ton inscription. Si tu souhaites la modifier, envoie nous un mail à l'adresse <a href="mailto:jsp@binets.polytechnique.fr?Subject=<?php echo urlencode("[JSP] Demande de modification d'inscription"); ?>">jsp@binets.polytechnique.fr</a>.</p>
			<div id="saveButtonWrapper">
				<input id="cancelButton" class="warningButton" type="button" onclick="showCancelReservationBox('<?php echo CancelationPage::getPage()->getPath(); ?>');" value="Annuler l'inscription" />
			</div>
			<?php } ?>
		</div>
	</div>
</div>