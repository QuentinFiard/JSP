<?php 
use database\Database;

use structures\events\SemaineReveillon;

use utilities\Server;

require_once 'classes/utilities/Server.php';
require_once 'classes/structures/events/SemaineReveillon.php';
require_once 'classes/database/Database.php';

global $user;
?>
<div class="details">
	<div class="content">
		<img class="logo" src="<?php echo Server::getServerRoot();?>img/logo_reveillon_500.png" />
		<h2>Description de la semaine</h2>
		<img id="laplagne_logo" class="shadow" src="<?php echo Server::getServerRoot();?>img/laplagne_logo.jpg" />
		<p>En plus du week-end homonyme, le binet JSP propose à des tarifs plus qu'avantageux une semaine de réveillon au ski du 29 décembre au 5 janvier à La Plagne, au cours de laquelle tu es totalement libre !</p>
		<p>La semaine est ouverte à toutes et à tous. Que tu sois un dieu de la glisse ou un débutant,  une çorde (à pointes), un taudis de combat ou même Grouze, la station t’accueillera à bras ouverts.</p>
	
		<p class="quote"><em>&laquo; C’était vraiment la meilleure semaine de ma vie. Si seulement je m’en souvenais. &raquo;</em>, Jean de la Corme.</p>
		<p class="quote"><em>&laquo; J’en ai pris plein les yeux ! &raquo;</em>, Gilbert Montagné.</p>
		<p class="quote"><em>&laquo; Mythe mec &raquo;</em>, un StyxMan.</p>

		<h2>La station</h2>
		
		<img id="plan_station" class="large_width shadow" src="<?php echo Server::getServerRoot();?>img/paradiski.jpg" />
 
		<a style="margin-bottom:15px;display:block;" href="http://www.la-plagne.com/fr/hiver/accueil-hiver.html">Site de la station de La Plagne</a>
		<p>Cette année, tu passeras une semaine entière de ski dans la station de La Plagne formant avec la station voisine des Arcs, le mythique domaine Paradiski qui vend encore plus de mythe que l’InfoStyx.</p>
		<p><em>&laquo; Situé au cœur des Alpes françaises, en Savoie, Paradiski est le paradis du skieur : un domaine culminant sur des glaciers à plus de 3000m, un enneigement assuré avec 70% des pistes au-dessus de 2000 mètres d’altitude, une liaison entre les stations des Arcs et de la Plagne par le plus gros téléphérique du monde : le Vanoise Express, une variété infinie de paysages et d’espaces de glisse, à expérimenter sur 425 km de pistes. &raquo;</em></p>
		<p>Bref pas de quoi t’ennuyer en une semaine !</p>
	


		<h2>Quelques chiffres :</h2>
		<ul>
			<li>425 km de pistes</li>
			<li>8 bars</li>
			<li>1 bowling, 1 ciné, 1 boîte</li>
			<li>21 restaurants</li>
			<li>Des stades de slalom, des bordercross, des snowparks, des piscines, des spas...</li>
		</ul>
		
		<img class="large_width shadow" id="laplagne_bob" src="<?php echo Server::getServerRoot();?>img/laplagne_bob.jpg" />
 		<div class="caption">Ils ont même prévu le BôB !</div>

		<h2>L’accès</h2>

		<p>Un des gros points forts de La Plagne est l’accès. Il suffit de prendre le train jusqu’à la gare d’Aime La Plagne, puis un service de bus assure la liaison avec la station.</p>
 
		<img id="tartiflette" class="shadow" src="<?php echo Server::getServerRoot();?>img/tartiflette.jpg" />
 		
		<div id="prix_sejour">
			<h2>Prix du séjour :</h2>
			<p><?php echo SemaineReveillon::shared()->getPriceAdherentKes(); ?> € pour tout le monde.</p>
		</div>
 
		<h2>La formule</h2>

		<p>Ce qui effraie souvent les gens est l’idée de partir une énième fois avec une association de l’école, et de ne pas être libre pendant la semaine. Détends-toi, l’idée est toute autre. Le binet sert juste d’intermédiaire avec l’agence de voyage pour te faire profiter de tarifs de groupe et t’offrir une semaine de ski à bas prix. Une fois sur place, on vous donne les clés de vos piaules et on vous laisse libre comme l’air&nbsp;! Si tu ne veux voir personne et skier all day long, tu peux. Tu peux aussi profiter de l’occasion pour partir une semaine aux sports d’hiver faire la fête avec tes potes, surtout que cette année, le réveillon du 31 décembre est inclus dans le séjour !</p>

		<p>N’importe qui peut venir, tes potes de prépa, ta copine, ta famille... On laisse ouvert un certain nombre de places aux extérieurs sans restriction, sur la base du shotgun. Si la demande devient trop importante, on privilégiera quand même les X sur liste d’attente pour éviter de faire une semaine avec plus d’extérieurs que d’X.</p>

		<p>Le prix comprend forfait et hébergement. Il y aussi aussi possibilité de rajouter des paniers-repas, des assurances diverses et des locations de skis. N’oublie pas de prévoir le transport aussi. Enfin, quelques activités seront organisées par l’agence.</p>

		<h2>Les inscriptions</h2>

		<p>Les inscriptions ouvriront le jeudi 15 novembre à minuit sur ce même site. Les premiers inscrits seront sur liste principale, les suivants sur liste d’attente. Le nombre total de place est de <?php echo SemaineReveillon::shared()->getNbOfPlaces(); ?>.
		<p>Si tu as des questions n’hésite pas à les poser sur <a href="http://www.facebook.com/JSP.2013">la page facebook des JSP</a> !</p>
		<p>Tu peux également écrire à <a href="mailto:camille.spaeth@polytechnique.edu?Subject=<?php echo urlencode('[JSP] Semaine du réveillon'); ?>">Camille Spaeth</a> si quelque chose ne te paraît pas clair (mais faut quand même que ce soit important).</p>
	</div>
</div>
