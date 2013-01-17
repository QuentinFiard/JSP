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
<div class="content" id="roomsContent">
	<input id="eventButton" type="hidden" value="buttonEvent<?php echo $event->getEventId(); ?>" />
	<img draggable="false" id="background" src="<?php echo Server::getServerRoot(); ?>img/background4.jpg" />
	<div class="outer_wrapper">
		<div class="wrapper">
			<div class="title">Choisis ta chambre !</div>
			<div class="content" id="mainContent">
				<?php 
					$rooms = $event->getRooms();
					$usersWithNoRoom = $event->getUsersWithNoRoom();
					$nbOfPlaces = null;
					$first = true;
					$currentRoom = $user->getRoomForEvent($event);
					foreach($rooms as $room)
					{
						$tmpNbOfPlaces = $room->getNbOfPlaces();
						if($nbOfPlaces!=$tmpNbOfPlaces)
						{
							$nbOfPlaces = $tmpNbOfPlaces;
							if(!$first)
							{
							?>
				</section>
							<?php
							}
							?>
				<section class="room_section">
					<div class="title">Chambres de <?php echo $nbOfPlaces; ?></div>
							<?php
							$first = false;
						}
						?>
					<form method="post" action="<?php echo $this->getPath(); ?>" onsubmit="return false;">
						<?php 
						if($currentRoom==$room)
						{
						?>
						<input type="hidden" name="unsetRoom" value="true">
						<?php
						}
						else
						{
						?>
						<input type="hidden" name="setRoom" value="true">
						<?php
						}
						?>
						<input type="hidden" name="roomId" value="<?php echo $room->getRoomId(); ?>">
					<div class="room">
						<div class="title">
						<?php
							$title = htmlspecialchars($room->getName());
							if(!$title)
							{
								$title = "Chambre n°".$room->getRoomNumber();
							}
							if($currentRoom==$room)
							{
							?>
							<input type="text" value="<?php echo $title; ?>" />
							<?php
							}
							else
							{
								echo $title;
							}
						?></div>
						<div class="header">
							<img src="<?php echo Server::getServerRoot(); ?>img/snowy_house_top.png" />
						</div>
						<div class="content">
						<?php 
							$members = $room->getMembers();
							$nbOfMembers = 0;
							$isInRoom = false;
							foreach($members as $member)
							{
								if($member->getUserId()==$user->getUserId())
								{
									$isInRoom = true;
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
								if($isInRoom)
								{
							?>
							<div class="member select">
								<select>
									<option value="">Place disponible</option>
									<?php 
									foreach($usersWithNoRoom as $userWithNoRoom)
									{
									?>
									<option value="<?php echo $userWithNoRoom->getUserId(); ?>"><?php echo $userWithNoRoom->getFullName(); ?></option>
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
							<img src="<?php echo Server::getServerRoot(); ?>img/snowy_house_bottom.png" />
						</div>
						<div class="button">
						
						<?php
							if($user->hasReservationForEvent($event))
							{
								if($isInRoom)
								{
								?>
								<input class="warningButton" type="button" value="Se désinscrire" onclick="submitRoomForm($(this).parents('form'));" />
								<?php
								}
								else if($currentRoom)
								{
								?>
								<input class="primaryButton" type="button" value="Changer de chambre" onclick="submitRoomForm($(this).parents('form'));" />
								<?php
								}
								else
								{
								?>
								<input class="primaryButton" type="button" value="S'inscrire" onclick="submitRoomForm($(this).parents('form'));" />
								<?php
								}
							}
						?>
						</div>
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