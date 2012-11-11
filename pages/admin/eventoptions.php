<?php
use structures\Option;

use utilities\Server;

require_once('classes/utilities/Server.php');
require_once 'classes/pages/connexion/ExterieursPage.php';
require_once 'classes/structures/Option.php';

use \pages\connexion\ExterieursPage;

global $event;
?>

<div class="content admin" id="adminContent">
	<section>
		<div class="title"><?php echo $event->getName(); ?></div>
		<section>
			<div class="title">Liste des options</div>
			<table border="1">
			<?php 
				$options = $event->getOptions();
				?><tr><?php
				foreach(Option::getProperties() as $key)
				{
					?><th><?php echo htmlspecialchars($key); ?></th><?php
				}
				?></tr><?php
				foreach($options as $option)
				{
					?><tr id="option<?php echo $option->getOptionId(); ?>"><?php
					foreach($option->getProperties() as $key)
					{
						?><td><?php echo $option->getProperty($key); ?></td><?php
					}
					?></tr><?php
				}
			?>
			</table>
		</section>
		<section>
			<div class="title">Modifier une option</div>
			<form method="post" action="<?php 
							global $currentPage;
							echo $currentPage->getPath();
						?>">
				<select name="optionId" required="required" onchange="changeSelectedOption(this);">
					<option value=""></option>
				<?php 
					$options = $event->getOptions();
					foreach($options as $option) {
					?>
					<option value="<?php echo $option->getOptionId(); ?>"><?php echo $option->getName(); ?></option>
				<?php } ?>
				</select>
				<div id="optionDetails">
					<div class="field">
						<label for="editOptionName">Nom</label>
						<input id="editOptionName" required="required" type="text" name="name" value="" />
					</div>
					<div class="field">
						<label for="editOptionDescription">Description</label>
						<textarea id="editOptionDescription" name="description"></textarea>
					</div>
					<div class="field">
						<label for="editOptionPrixAdherent">Prix adhérent</label>
						<input id="editOptionPrixAdherent" required="required" type="text" name="price_x" value="" />
					</div>
					<div class="field">
						<label for="editOptionPrixExt">Prix extérieur</label>
						<input id="editOptionPrixExt" required="required" type="text" name="price_ext" value="" />
					</div>
					<input type="hidden" name="updateOption" value="true" />
					<input type="submit" value="Confirmer les modifications" />
				</div>
			</form>
		</section>
		<section>
			<div class="title">Ajouter une option</div>
			<form method="post" action="<?php 
							global $currentPage;
							echo $currentPage->getPath();
						?>">
				<div id="optionDetails">
					<div class="field">
						<label for="addOptionName">Nom</label>
						<input id="addOptionName" required="required" type="text" name="name" value="" />
					</div>
					<div class="field">
						<label for="addOptionDescription">Description</label>
						<textarea id="addOptionDescription" name="description"></textarea>
					</div>
					<div class="field">
						<label for="addOptionPrixAdherent">Prix adhérent</label>
						<input id="addOptionPrixAdherent" required="required" type="text" name="price_x" value="" />
					</div>
					<div class="field">
						<label for="addOptionPrixExt">Prix extérieur</label>
						<input id="addOptionPrixExt" required="required" type="text" name="price_ext" value="" />
					</div>
					<input type="hidden" name="addOption" value="true" />
					<input type="submit" value="Ajouter" />
				</div>
			</form>
		</section>
		<section>
			<div class="title">Supprimer une option</div>
			<form method="post" action="<?php 
							global $currentPage;
							echo $currentPage->getPath();
						?>">
				<select name="optionId" onchange="changeSelectedOption(this);">
				<?php 
					$options = $event->getOptions();
					foreach($options as $option) {
					?>
					<option value="<?php echo $option->getOptionId(); ?>"><?php echo $option->getName(); ?></option>
				<?php } ?>
				</select>
				<input type="hidden" name="dropOption" value="true" />
				<input type="submit" value="Supprimer" />
			</form>
		</section>
	</section>
</div>