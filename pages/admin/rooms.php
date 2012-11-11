<?php
use utilities\Server;

require_once('classes/utilities/Server.php');
require_once 'classes/pages/connexion/ExterieursPage.php';

use \pages\connexion\ExterieursPage;

global $event;
?>

<div class="content admin" id="adminContent">
	<section>
		<div class="title"><?php echo $event->getName(); ?> : Gestion globale des chambres</div>
		<section>
			<div class="title">État actuel des réservations</div>
			Nombre et types de chambres :
			<table border="1">
				<tr>
					<th>Nombre de places</th>
					<th>Nombre de chambres</th>
				</tr>
				<?php 
					$report = $event->getRoomReport();
					foreach($report['rooms'] as $nbOfPlaces => $nbOfRooms)
					{
				?>
					<tr>
						<td><?php echo $nbOfPlaces; ?></td>
						<td><?php echo $nbOfRooms; ?></td>
					</tr>
				<?php } ?>
			</table>
			Nombre et types des chambres <b>vides</b> :
			<table border="1" style="margin-top:5px">
				<tr>
					<th>Nombre de places</th>
					<th>Chambres vides</th>
				</tr>
				<?php 
					$report = $event->getRoomReport();
					foreach($report['unused'] as $nbOfPlaces => $nbOfRooms)
					{
				?>
					<tr>
						<td><?php echo $nbOfPlaces; ?></td>
						<td><?php echo $nbOfRooms; ?></td>
					</tr>
				<?php } ?>
			</table><br />
			Nombre de places total pour l'évênement : <b><?php echo $event->getNbOfPlaces(); ?> places</b><br /><br />
			Nombre d'inscrits à l'évênement : <b><?php echo $event->getNbOfUserWithReservation(); ?> inscrits</b>
		</section>
		<section>
			<div class="title">Modifier le nombre de chambres</div>
			<section>
				<div class="title">Ajouter des chambres</div>
				<form method="post" action="<?php 
						global $currentPage;
						echo $currentPage->getPath();
					?>">
					<div class="field">
						<label for="nbOfPlacesByRoomToAdd">Nombre de places</label>
						<select id="nbOfPlacesByRoomToAdd" name="nbOfPlaces">
							<?php for($i=1 ; $i<20 ; $i++) {?>
							<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="field">
						<label for="nbOfRoomsToAdd">Nombre de chambres</label>
						<input id="nbOfRoomsToAdd" name="nbOfRooms" type="number" value="1" />
					</div>
					<input type="hidden" name="addRooms" value="true" />
					<input type="submit" value="Ajouter les chambres" />
				</form>
			</section
			><section>
				<div class="title">Supprimer des chambres</div>
				<form method="post" action="<?php 
						global $currentPage;
						echo $currentPage->getPath();
					?>">
					<div class="field">
						<label for="nbOfPlacesByRoom">Type de chambres</label>
						<select id="nbOfPlacesByRoom" name="nbOfPlaces">
							<?php 
							$report = $event->getRoomReport();
							$unused = $report['unused'];
							foreach($unused as $nbOfPlaces => $nbOfRooms)
							{ 
								if($nbOfRooms>0) {?>
							<option value="<?php echo $nbOfPlaces; ?>"><?php echo $nbOfPlaces; ?> places</option>
							<?php }
							} ?>
						</select>
					</div>
					<div class="field">
						<label for="nbOfRoomsToRemove">Nombre de chambres à supprimer</label>
						<input id="nbOfRoomsToRemove" name="nbOfRooms" type="number" value="1" />
					</div>
					<input type="hidden" name="removeRooms" value="true" />
					<input type="submit" value="Supprimer les chambres" />
				</form>
			</section>
		</section>
	</section>
	<section>
		<div class="title">Modifier une chambre</div>
		<form method="post" action="<?php 
						global $currentPage;
						echo $currentPage->getPath();
					?>">
			<select name="selectedRoomId" onchange="changeSelectedRoom(this);">
				<?php 
				global $selectedRoom;
				$rooms = $event->getRooms();
				foreach($rooms as $room) {
				?>
				<option<?php 
					if($selectedRoom == $room)
					{
						echo ' selected="selected"';
					}
				?> value="<?php echo $room->getRoomId(); ?>">Chambre n°<?php echo $room->getRoomNumber(); ?><?php 
					if($room->getName()!=null)
					{
						echo ' "'.$room->getName().'"';
					}
				?> : <?php echo $room->getNbOfMembers(); ?> incrits, <?php echo $room->getNbOfPlaces(); ?> places</option>
				<?php } ?>
			</select>
			<?php if(count($rooms)>0) { ?>
			<?php
				$room = $rooms[0];
				
				if(isset($selectedRoom) && in_array($selectedRoom, $rooms))
				{
					$room = $selectedRoom;
				}
			?>
			<div id="roomDetails">
				<div class="field">
					<label for="roomName">Nom de la chambre</label>
					<input id="roomName" name="name" value="<?php echo htmlspecialchars($room->getName()); ?>" />
				</div>
				<div class="field">
					<label for="nbOfPlacesInRoom">Nombre de places</label>
					<input id="nbOfPlacesInRoom" name="nbOfPlaces" type="number" value="<?php echo $room->getNbOfPlaces(); ?>" />
				</div>
				<div class="field">
					<label for="roomNumber">Numéro de chambre</label>
					<input id="roomNumber" name="roomNumber" type="number" value="<?php echo $room->getRoomNumber(); ?>" />
				</div>
				<input type="hidden" name="modifyRoom" value="true" />
				<input type="submit" value="Confirmer les modifications" />
				<?php } ?>
			</div>
		</form>
	</section>
</div>