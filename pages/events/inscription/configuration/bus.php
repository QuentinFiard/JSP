<?php

use structures\events\SemaineReveillon;
use structures\events\WeekendJSP;

use utilities\Server;
require_once 'classes/utilities/Server.php';
require_once 'classes/structures/events/WeekendJSP.php';
require_once 'classes/structures/events/SemaineReveillon.php';

global $user;
global $currentPage;
$event = $this->getEvent();
?>
<div class="content" id="busesContent">
	<input id="eventButton" type="hidden" value="buttonEvent<?php echo $event->getEventId(); ?>" />
	<img draggable="false" id="background" src="<?php echo Server::getServerRoot(); ?>img/background4.jpg" />
	<div class="outer_wrapper">
		<div class="wrapper">
			<div class="title">Choisis ton bus !</div>
			<div class="content" id="mainContent">
				<?php 
					$buses = $event->getBuses();
					$usersWithNoBuses = $event->getUsersWithNoBus();
					$first = true;
					$currentBus = $user->getBusForEvent($event);
					$busIndex=0;
					foreach($buses as $bus)
					{
						$nbOfPlaces = $bus->getNbOfPlaces();
						?>
					<form method="post" action="<?php echo $this->getPath(); ?>" onsubmit="return false;">
						<?php 
						if($currentBus==$bus)
						{
						?>
						<input type="hidden" name="unsetBus" value="true">
						<?php
						}
						else
						{
						?>
						<input type="hidden" name="setBus" value="true">
						<?php
						}
						?>
						<input type="hidden" name="busId" value="<?php echo $bus->getBusId(); ?>">
					<div class="bus"<?php if($busIndex%3==0){?> style="clear:left;"<?php }?>>
						<div class="title">
						<?php
							$busIndex++;
							$title = htmlspecialchars($bus->getName());
							if(!$title)
							{
								$title = "Bus n°".$bus->getBusNumber();
							}
							echo $title;
						?></div>
						<?php ob_start(); ?>
						<div class="header">
							<img src="<?php echo Server::getServerRoot(); ?>img/bus_top.png" />
						</div>
						<div class="content">
						<?php 
							$members = $bus->getMembers();
							$nbOfMembers = 0;
							$isInBus = false;
							foreach($members as $member)
							{
								if($member->getUserId()==$user->getUserId())
								{
									$isInBus = true;
								}
							?>
							<div class="member">
								<?php echo $member->getFullName(); ?>
							</div>
							<?php
								$nbOfMembers++;
							}
							
							for($i=0 ; $i<$nbOfPlaces-$nbOfMembers ; $i++)
							{
								if($isInBus && $i==0)
								{
							?>
							<div class="member select">
								<select>
									<option value="">Place disponible</option>
									<?php 
									foreach($usersWithNoBuses as $userWithNoBus)
									{
									?>
									<option value="<?php echo $userWithNoBus->getUserId(); ?>"><?php echo $userWithNoBus->getFullName(); ?></option>
									<?php
									}
									?>
								</select>
							</div>
							<?php
								}
								else
								{
							?>
							<div class="member dispo">
								Place disponible
							</div>
							<?php		
								}
							}
						?>
						</div>
						<div class="footer">
							<img src="<?php echo Server::getServerRoot(); ?>img/bus_bottom.png" />
						</div>
						<?php
							$content = ob_get_contents(); 
							ob_end_clean();
						?>
						<div class="button">
						<?php
							if($user->hasReservationForEvent($event))
							{
								if($isInBus)
								{
								?>
								<input class="warningButton" type="button" value="Se désinscrire" onclick="submitBusForm($(this).parents('form'));" />
								<?php
								}
								else if($currentBus)
								{
								?>
								<input class="primaryButton" type="button" value="Changer de bus" onclick="submitBusForm($(this).parents('form'));" />
								<?php
								}
								else
								{
								?>
								<input class="primaryButton" type="button" value="S'inscrire" onclick="submitBusForm($(this).parents('form'));" />
								<?php
								}
							}
						?>
						</div>
						<?php echo $content ?>
					</div>
					</form>
					<?php
					}
				?>
			</div>
			<div class="footer"></div>
		</div>
		<div id="back_arrow" onclick="goToPage('<?php echo $this->getParent()->getPath(); ?>');">
			<div class="title">Retour</div>
			<div class="img_wrapper">
				<img src="<?php echo Server::getServerRoot(); ?>img/arrow_left.png" />
			</div>
		</div>
	</div>
	
</div>