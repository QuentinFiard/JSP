<?php 
use pages\events\WeekendPage;
use utilities\Server;

use pages\events\ReveillonPage;

require_once 'classes/pages/events/ReveillonPage.php';
require_once 'classes/pages/events/WeekendPage.php';
require_once 'classes/structures/events/WeekendJSP.php';
require_once 'classes/structures/events/SemaineReveillon.php';
require_once('classes/utilities/Server.php');

use \pages\events;
use \structures\events\SemaineReveillon;
use \structures\events\WeekendJSP;

global $user;
?>
<div class="content" id="eventsContent">
	<img draggable="false" id="background" src="img/background1.jpg" />
	<div id="events_wrapper">
		<div id="events">
			<?php 
				$event = SemaineReveillon::shared();
				$hasReservation = $user->hasReservationForEvent($event);
				$hasToPay = $user->hasToPayForEvent($event);
				$reservationComplete = $user->isReservationCompleteForEvent($event);
				$waitingList = null;
				if($hasReservation)
				{
					$waitingList = $user->isOnWaitingListForEvent($event);
				}
				
				$options = $user->getOptionsForEvent($event);
				
				$subvention_name = "none";
				$subvention_value = 0;
				$subvention_option = null;
				
				foreach($options as $option)
				{
					$pos = strpos($option->getName(),'subvention');
					if(!($pos===false))
					{
						$subvention_option = $option;
						$subvention_name = $option->getName();
						$subvention_value = $option->getPriceForUser($user);
					}
				}
			?>
			<div class="event" id="reveillon">
				<input type="hidden" class="path" value="<?php echo htmlspecialchars($event->getPagePath()); ?>" />
				<div class="overlay"></div>
				<div class="content">
					<div class="title"><?php echo $event->getName(); ?></div>
					<img class="logo" src="<?php echo Server::getServerRoot(); ?>img/logo_reveillon.png" />
					<div class="infos">
						<div class="title">Dates</div>
						<div class="content">Du 29 décembre 2012 au 5 janvier 2013</div>
					</div>
					<div class="infos">
						<div class="title">Lieu</div>
						<div class="content">La Plagne</div>
					</div>
					<div class="infos">
						<?php if($reservationComplete) { ?>
						<div class="title">Montant payé</div>
						<div class="content"><?php echo $event->priceForUser($user); ?> € + <?php echo $event->cautionForUser($user); ?> € de caution</div>
						<?php }
						else if($hasToPay) { ?>
						<div class="title">Montant à payer</div>
						<div class="content">Binet JSP : <?php echo $event->priceForUser($user); ?> €<?php if($subvention_option){ echo ' + '.(-$subvention_value).' € (non encaissé)'; } ?><br/>
											<?php 
												if($event==WeekendJSP::shared())
												{
													?>'Madame Vacances'<?php
												} else {
													?>Belhambra<?php
												} ?> : <?php echo $event->cautionForUser($user); ?> € de caution</div>
						<?php } else { ?>
						<div class="title">Prix de base</div>
						<div class="content"><?php echo $event->priceForUser($user); ?> €</div>
						<?php } ?>
					</div>
				</div>
				<?php 
					if($reservationComplete) { 
						if($waitingList) {
							?><div class="status registered">Paiement reçu, inscrit sur liste d'attente.</div><?php
						}
						else {
							?><div class="status registered">Tu es inscrit(e) à cet évênement !</div><?php
						}
					}
					else if($hasToPay) {
						?><div class="status waitingForPayment">En attente de réception du paiement.</div><?php
					}
					else
					{
						?><div class="status notregistered">Tu n'es pas encore inscrit(e) à cet évênement.</div><?php
					}
					
					if($hasReservation) {
					?>
					
					<div class="badge_wrapper<?php if($waitingList){?> full<?php } ?>">
						<div class="badge"><?php
							if($waitingList)
							{
								?>Liste d'attente ! (Rang <?php echo $event->getPositionInWaitingListForUser($user)+1; ?> sur <?php echo $event->getNbOfReservationsInWaitingList(); ?>)<?php
							}
							else 
							{
								?>Liste principale !<?php if($user->isAdmin())
								{
									?> (<?php
									$placesLeft = $event->getNbOfPlacesLeft();
									if($placesLeft>=0)
									{
										echo $placesLeft;
										?> place<?php if($placesLeft>1){?>s<?php }?> dispo<?php
									}
									else
									{
										echo $event->getNbOfReservationsInWaitingList();
										?> sur liste d'attente<?php
									}
									?>)<?php
								}
							}
							?></div>
					</div>
					<?php
					}
					else
					{
						$placesLeft = $event->getNbOfPlacesLeft();
						if($event->haveReservationsStarted() || $user->isAdmin())
						{?>
					
					<div class="badge_wrapper<?php if($placesLeft<=0){?> full<?php } ?>">
						<div class="badge"><?php
							if($placesLeft<=0)
							{
								?>Liste d'attente !<?php if($user->isAdmin()){?> (<?php echo $event->getNbOfReservationsInWaitingList(); ?>)<?php } ?><?php
							} else if($placesLeft<10)
							{
								?>Plus que <?php echo $placesLeft; ?> place<?php if($placesLeft>1){?>s<?php } ?> !<?php
							} else {
								?>Encore <?php echo $placesLeft; ?> places<?php
							} ?></div>
					</div>
					
					<?php
						}
					}
				?>
				<div class="arrow inscription">
					<div class="title"><?php if($hasReservation) {?>Mon inscription<?php } else { ?>S'inscrire<?php } ?></div>
					<div class="img_wrapper">
						<img src="<?php echo Server::getServerRoot(); ?>img/arrow_right.png" />
					</div>
				</div>
				<div class="arrow back">
					<div class="title">Retour</div>
					<div class="img_wrapper">
						<img src="<?php echo Server::getServerRoot(); ?>img/arrow_left.png" />
					</div>
				</div>
			</div>
			<?php 
				if($user->isAdherentKes()) {
			?>
			<?php $event = WeekendJSP::shared();
				$hasReservation = $user->hasReservationForEvent($event);
				$hasToPay = $user->hasToPayForEvent($event);
				$reservationComplete = $user->isReservationCompleteForEvent($event);
				$waitingList = null;
				if($hasReservation)
				{
					$waitingList = $user->isOnWaitingListForEvent($event);
				}
				
				$options = $user->getOptionsForEvent($event);
				
				$subvention_name = "none";
				$subvention_value = 0;
				$subvention_option = null;
				
				foreach($options as $option)
				{
					$pos = strpos($option->getName(),'subvention');
					if(!($pos===false))
					{
						$subvention_option = $option;
						$subvention_name = $option->getName();
						$subvention_value = $option->getPriceForUser($user);
					}
				}
			?>
			<div class="event" id="weekend">
				<input type="hidden" class="path" value="<?php echo htmlspecialchars($event->getPagePath()); ?>" />
				<div class="overlay"></div>
				<div class="content">
					<div class="title"><?php echo $event->getName(); ?></div>
					<img class="logo" src="img/logo_weekend.png" />
					<div class="infos">
						<div class="title">Dates</div>
						<div class="content">Du 12 au 14 janvier 2013</div>
					</div>
					<div class="infos">
						<div class="title">Lieu</div>
						<div class="content">SuperDévoluy</div>
					</div>
					<div class="infos">
						<?php if($reservationComplete) { ?>
						<div class="title">Montant payé</div>
						<div class="content"><?php echo $event->priceForUser($user); ?> € + <?php echo $event->cautionForUser($user); ?> € de caution</div>
						<?php }
						else if($hasToPay) { ?>
						<div class="title">Montant à payer</div>
						<div class="content">Binet JSP : <?php echo $event->priceForUser($user); ?> €<?php if($subvention_option){ echo ' + '.(-$subvention_value).' € (non encaissé)'; } ?><br/>
											<?php 
												if($event==WeekendJSP::shared())
												{
													?>'Madame Vacances'<?php
												} else {
													?>Belhambra<?php
												} ?> : <?php echo $event->cautionForUser($user); ?> € de caution</div>
						<?php } else { ?>
						<div class="title">Prix de base</div>
						<div class="content"><?php echo $event->priceForUser($user); ?> €</div>
						<?php } ?>
					</div>
				</div>
				<?php 
					if($reservationComplete) { 
						if($waitingList) {
							?><div class="status registered">Paiement reçu, inscrit sur liste d'attente.</div><?php
						}
						else {
							?><div class="status registered">Tu es inscrit(e) à cet évênement !</div><?php
						}
					}
					else if($hasToPay) {
						?><div class="status waitingForPayment">En attente de réception du paiement.</div><?php
					}
					else
					{
						?><div class="status notregistered">Tu n'es pas encore inscrit(e) à cet évênement.</div><?php
					}
					
					if($hasReservation) {
					?>
					
					<div class="badge_wrapper<?php if($waitingList){?> full<?php } ?>">
						<div class="badge"><?php
							if($waitingList)
							{
								?>Liste d'attente ! (Rang <?php echo $event->getPositionInWaitingListForUser($user)+1; ?> sur <?php echo $event->getNbOfReservationsInWaitingList(); ?>)<?php
							}
							else 
							{
								?>Liste principale !<?php if($user->isAdmin())
								{
									?> (<?php
									$placesLeft = $event->getNbOfPlacesLeft();
									if($placesLeft>=0)
									{
										echo $placesLeft;
										?> place<?php if($placesLeft>1){?>s<?php }?> dispo<?php
									}
									else
									{
										echo $event->getNbOfReservationsInWaitingList();
										?> sur liste d'attente<?php
									}
									?>)<?php
								}
							}
							?></div>
					</div>
					<?php
					}
					else
					{
						$placesLeft = $event->getNbOfPlacesLeft();
						if($event->haveReservationsStarted() || $user->isAdmin())
						{?>
					
					<div class="badge_wrapper<?php if($placesLeft<=0){?> full<?php } ?>">
						<div class="badge"><?php
							if($placesLeft<=0)
							{
								?>Liste d'attente !<?php if($user->isAdmin()){?> (<?php echo $event->getNbOfReservationsInWaitingList(); ?>)<?php } ?><?php
							} else if($placesLeft<10)
							{
								?>Plus que <?php echo $placesLeft; ?> place<?php if($placesLeft>1){?>s<?php } ?> !<?php
							} else {
								?>Encore <?php echo $placesLeft; ?> places<?php
							} ?></div>
					</div>
					
					<?php
						}
					}
				?>
				<div class="arrow inscription">
					<div class="title"><?php if($hasReservation) {?>Mon inscription<?php } else { ?>S'inscrire<?php } ?></div>
					<div class="img_wrapper">
						<img src="<?php echo Server::getServerRoot(); ?>img/arrow_right.png" />
					</div>
				</div>
				<div class="arrow back">
					<div class="title">Retour</div>
					<div class="img_wrapper">
						<img src="<?php echo Server::getServerRoot(); ?>img/arrow_left.png" />
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>