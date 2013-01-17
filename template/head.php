<?php
use utilities\Server;

global $currentPage;

use structures\events\SemaineReveillon;
use structures\events\WeekendJSP;

use pages\events\ReveillonPage;
use pages\events\WeekendPage;

use \nav\Root;

use \process\Frankiz;
use \structures\User;

use \pages\AdminPage;
use \pages\MyAccountPage;

require_once('classes/process/Frankiz.php');
require_once('classes/database/Database.php');

require_once('classes/nav/Root.php');
require_once('classes/pages/events/WeekendPage.php');
require_once('classes/pages/events/ReveillonPage.php');
require_once('classes/pages/EventsPage.php');
require_once('classes/pages/AdminPage.php');
require_once('classes/pages/MyAccountPage.php');
require_once('classes/structures/events/SemaineReveillon.php');
require_once('classes/structures/events/WeekendJSP.php');

require_once('classes/utilities/Server.php');

use \pages\events;

global $user;
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#" xml:lang="fr">
<head>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
    <title>JSP - Site d'inscription aux évênements</title>
    <meta name="author" content="Quentin Fiard - quentin.fiard@polytechnique.org" />
    <link rel="icon" href="<?php echo Server::getServerRoot(); ?>img/favicon.ico" sizes="16x16 32x32 48x48 64x64" type="image/vnd.microsoft.icon">
    
	<script type="text/javascript" src="<?php echo Server::getServerRoot(); ?>js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo Server::getServerRoot(); ?>js/jquery.easing.js"></script>
	<script type="text/javascript" src="<?php echo Server::getServerRoot(); ?>js/jquery.history.js"></script>
	<script type="text/javascript" src="<?php echo Server::getServerRoot(); ?>js/jquery.zoomooz.js"></script>
	<script type="text/javascript" src="<?php echo Server::getServerRoot(); ?>js/flipcounter.js"></script>
	<script type="text/javascript" src="<?php echo Server::getServerRoot(); ?>js/sha256.js"></script>
	
	<?php /*<script type="text/javascript" src="js/jquery.framerate.js"></script> */?>
	<?php /*<script type="text/javascript" src="js/jquery.animate-enhanced.min.js"></script> */ ?>
	<script type="text/javascript" src="<?php echo Server::getServerRoot(); ?>js/shared.js"></script>
	<script type="text/javascript" src="<?php echo Server::getServerRoot(); ?>js/trail.js"></script>
	<link href="<?php echo Server::getServerRoot(); ?>css/reset.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo Server::getServerRoot(); ?>css/gillsans.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo Server::getServerRoot(); ?>css/shared.css" rel="stylesheet" type="text/css" />
	<?php
		if(file_exists($currentPage->getPageStylePath())) { ?>
	<link href="<?php echo Server::getServerRoot().$currentPage->getPageStylePath(); ?>" rel="stylesheet" type="text/css" />
	<?php } ?>
</head>
<body>
	<div id="fb-root"></div>
	<script>
	  window.fbAsyncInit = function() {
	    FB.init({
	      appId      : '405053562899009', // App ID
	      //channelURL : '//WWW.YOUR_DOMAIN.COM/channel.html', // Channel File
	      status     : true, // check login status
	      cookie     : true, // enable cookies to allow the server to access the session
	      oauth      : true, // enable OAuth 2.0
	      xfbml      : true  // parse XFBML
	    });
	
	    //
	    // All your canvas and getLogin stuff here
	    //
	  };
	
	  // Load the SDK Asynchronously
	  (function(d){
	     var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
	     js = d.createElement('script'); js.id = id; js.async = true;
		 js.src = "//connect.facebook.net/fr_FR/all.js#xfbml=1";
	     d.getElementsByTagName('head')[0].appendChild(js);
	   }(document));
	</script>
	<header>
		<nav>
			<ol>
				<li id="buttonHomePage" <?php if($currentPage == Root::getPage() || $currentPage == \pages\EventsPage::getPage()){ ?>class="current" <?php } ?>onclick="handleNavButtonClick(this,'<?php echo Root::getPage()->getPath(); ?>');">
					<img class="logo" src="<?php echo Server::getServerRoot(); ?>img/logo_simple_200.png" />
					<a>Journées de ski polytechniciennes</a>
				</li>
				<?php if(isset($user) && $user->isRegistered()){?>
				<li id="buttonEvent<?php echo SemaineReveillon::shared()->getEventId(); ?>" <?php if($currentPage->getPath() == SemaineReveillon::shared()->getPagePath()){ ?>class="current" <?php } ?>onclick="handleNavButtonClick(this,'<?php echo SemaineReveillon::shared()->getPagePath(); ?>');"><a>Semaine du réveillon</a></li>
					<?php if($user->isAdherentKes()) { ?>
				<li id="buttonEvent<?php echo WeekendJSP::shared()->getEventId(); ?>" <?php if($currentPage->getPath() == WeekendJSP::shared()->getPagePath()){ ?>class="current" <?php } ?>onclick="handleNavButtonClick(this,'<?php echo WeekendJSP::shared()->getPagePath(); ?>');"><a>Weekend JSP</a></li>
					<?php } ?>
				<?php } ?>
				<?php if(isset($user) && $user->isMember()) {?>
				<li id="buttonAdmin" <?php if($currentPage->getPath() == AdminPage::getPage()){ ?>class="current" <?php } ?>onclick="handleNavButtonClick(this,'<?php echo AdminPage::getPage()->getPath(); ?>');"><a>Admin</a></li>
				<?php } ?>
				<li class="end">&nbsp;</li>
			</ol>
			<div id="facebook_like">
				<fb:like href="https://www.facebook.com/JSP.2013" send="false" layout="button_count" show_faces="true"></fb:like>
			</div>
		</nav>
		<ol class="buttons">
			<?php if(isset($user) && $user->isRegistered()){?>
			<li class="welcome">
			Bonjour <?php echo $user->displayName(); ?> !
			</li>
			<li class="defaultButton">
				<a onclick="goToPage('<?php echo MyAccountPage::getPage()->getPath(); ?>');">Mon compte</a>
			</li>
			<?php if($user->isFrankizUser()) {?>
			<li onclick="frankizLogout('<?php echo Server::getServerRoot(); ?>logout');" class="warningButton">
				<a>Déconnexion (Frankiz)</a>
			</li>
			<?php } else { ?>
			<li class="warningButton">
				<a href="<?php echo Server::getServerRoot(); ?>logout">Déconnexion</a>
			</li>
			<?php } ?>
			<?php } else { ?>
			<li class="primaryButton">
				<a href="<?php echo Server::getServerRoot(); ?>login">Connexion (Frankiz)</a>
			</li>
			<li id="external_login" class="primaryButton">
				<a>Connexion (Extérieurs)</a>
			</li>
			<?php } ?>
			<li class="end"></li>
		</ol>
	</header>
	<?php if(!isset($user) || !$user->isRegistered()){ ?>
	<div id="login_form">
		<div class="content">
			<div class="wrapper">
				<div class="title">
					Pas encore de compte ?
				</div>
				<div class="buttons">
					<div class="defaultButton">
						<a onclick="goToPage('/connexion/exterieurs/creercompte');">Créer un compte</a>
					</div>
				</div>
				<div class="title">
					Connexion
				</div>
				<form action="<?php echo Server::getServerRoot(); ?>login/ext" method="post">
					<input class="type" type="hidden" name="login" value="true" />
					<input class="sha" type="hidden" name="sha" value="false" />
					<fieldset>
						<div class="field">
							<label id="mailLabel" for="mail"><img src="<?php echo Server::getServerRoot(); ?>img/mail.png" /></label>
							<input id="mail" name="mail" type="email" required="required" placeholder="Adresse mail" />
						</div>
						<div class="field">
							<label id="passwordLabel" for="password"><img src="<?php echo Server::getServerRoot(); ?>img/key.png" /></label>
							<input id="password" name="password" type="password" required="required" placeholder="Mot de passe" />
						</div>
					</fieldset>
					<a class="forgottenPassword" onclick="showForgottenPasswordBox();">Mot de passe oublié ?</a>
					<div class="buttons">
						<input class="warningButton" type="button" value="Annuler" />
						<input class="primaryButton" type="submit" value="Connexion" />
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php } ?>
	<div id="contentWrapper">