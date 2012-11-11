<?php 
use database\Database;

use structures\events\WeekendJSP;

use utilities\Server;

require_once 'classes/utilities/Server.php';
require_once 'classes/structures/events/WeekendJSP.php';
require_once 'classes/database/Database.php';

global $user;
?>

<div class="details" id="details_weekend">
	<div class="content">
		<img class="logo" src="<?php echo Server::getServerRoot();?>img/logo_jsp_500.png" />
	
		<p>Les JSP, pour Journées de Ski Polytechniciennes, consistent en un long week-end de janvier au ski, avec compétitions et soirées inoubliables. Chaque année les JSP réunissent environ 500 élèves et amis d'élèves dans une station sympathique, pour trois jours et trois nuits de glisse et de fiesta.</p>
		<p>C'est un événement bipromo, on y va plutôt pour le ski ou plutôt pour la fête suivant les goûts ; c'est pension complète et activités à gogo, l'organisation s'occupe de tout ! Tu rêvais d’un WEI à la neige ? Les JSP l’ont fait !</p>
	
		<img id="superdevoluy" class="shadow" src="<?php echo Server::getServerRoot();?>img/superdevoluy.jpg" />
		
		<h3><b><i>
			EN BREF :
			Les JSP, c’est THE weekend, THE place to be en janvier 2013, THE événement bi-promo que vous n’oublierez jamais.
		</i></b></h3>
	
		<p class="quote"><em>&laquo; Le paradis du binet chute. Mythe. &raquo;</em>, H de March.</p>
		<p class="quote"><em>&laquo; Le télésiège s’est même pas bloqué ! &raquo;</em>, Jean-Claude Duss.</p>
		<p class="quote"><em>&laquo; Ça m’a rappelé Bagdad mon gars. Mais avec de la neige. &raquo;</em>, Grouze.</p>
		<p class="quote"><em>&laquo; Côt &raquo;</em>, Starchick.</p>
		
		<h2>JSP gets you to SuperDévoluy !</h2>
		
	
		<img id="superdevoluy_logo" src="<?php echo Server::getServerRoot();?>img/superdevoluy_logo.png" />
		
		<p>Cette année les JSP vous emmènent dans cette mythique et sympatique station des Hautes Alpes qu’est SuperDevoluy. Il s’agit d’une station famililale, ce qui permettra d’avoir une ambiance promâle et de se croiser souvent sur les pistes. De plus nous logerons tous dans le même bâtiment ! (cohèz mythe zdé plap)</p>
		
		<p>Et si tu veux chouffer l’état de la poudreuse sur le site voilà le lien de la webcam du site : <a href="http://www.ledevoluy.com/hiver/fr/infos/en-direct-du-devoluy/webcams.html">Webcams SuperDevoluy</a></p>
	 
		<h2>Au programme des réjouissances :</h2>
		<p>Départ en bus le vendredi 11 janvier au soir pour une nuit de folie.</p>
		<p>La SNCF ayant arrêtée son activité train disco, le départ se fera donc en bus. C’est la fin d’une ère mais le début d’une autre, et compte sur l’équipe des JSP pour que le mythe et l’ambiancement soit au rendez-vous comme jamais !</p>
		<p>À votre arrivée à la station un petit déjeuner vous attendra, vos appartements seront fins prêts pour vous accueillir, et les pistes fraichement damées pour vous faire skier ! Sans oublier les services de location de matos.</p>
		<p>Dès l’ouverture des pistes vous pourrez profiter du domaine skiable et des nombreuses activités organisées par le binet JSP avec entre autres un Big Air, une flèche ESF, des cours de ski, un after-ski vin chaud en bas des pistes, un trophée chartreuse.</p>
		<p>Le samedi soir et le dimanche soir, deux soirées à thème de folie vous attendent, préparez vos déguisements ! Les thèmes des soirées vous seront dévoilés en temps et en heure...</p>
		<p>Le retour s’effectuera le lundi aux alentours de 16h : les bus de l’aller nous récupèreront pour un trajet retour garanti sans vomi sur le chauffeur.</p>
	
		<h2>Le contrat de confiance JSP :</h2>
		<p>Comme l’aurez compris, tout est inclus : transport, hébergement, forfait, nourriture, alcool, ambiancement et mythe, le tout pour les 3 jours et 3 nuits du weekend.</p>
		<p>Nous vous proposons également un service de location du matériel de ski, auquel vous pourrez souscrire au moment de votre inscription.</p>
	
		<div id="prix_sejour">
			<h2>Prix du séjour :</h2>
			<p><?php 
				$prix_base = WeekendJSP::shared()->priceForUser($user);
				echo $prix_base; 
				?> € de base (sans location de matériel)<?php 
				$database = Database::shared();
				if($user->is2010() && $database->hasConfigurationField('subvention_kes_vos')) {
					$prix = $prix_base - $database->getConfigurationFieldAsDouble('subvention_kes_vos');
				?>, <?php echo $prix; ?> € si tu n'es pas allé au VOS<?php 
				}
				?>.
			</p>
		</div>
	</div>
</div>
