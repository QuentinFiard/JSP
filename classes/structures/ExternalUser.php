<?php

namespace structures;

use utilities\Server;

use database\Database;

require_once ('classes/structures/User.php');
require_once ('classes/database/Database.php');
require_once 'classes/utilities/Server.php';

use structures\User;

class ExternalUser extends User {
	private $validated;
	private $salt;
	private $digest;
	private $isCadreX;

	public function __construct($data) {
		$this->updateWithData($data,true);
	}
	
	public function updateWithData($data,$constructor=false)
	{
		parent::updateWithData($data,$constructor);
		$properties = get_class_vars(get_class());
		foreach($properties as $key => $default_value)
		{
			if(array_key_exists($key, $data))
			{
				$this->$key = $data[$key];
			}
			else if($constructor)
			{
				$this->$key = null;
			}
		}
	}
	
	public function getUserConfirmationId()
	{
		return substr(bin2hex($this->salt), 0, 32);
	}
	
	public function resetPassword()
	{
		Database::shared()->validateExternalUser($this);
		$newPassword = Database::shared()->resetPasswordForExternalUser($this);
		$this->sendResetPasswordEmail($newPassword);
	}
	
	public function getEmailValidationURL()
	{
		return 'http://jsp.binets.fr/mailconfirmation?user='.$this->getUserConfirmationId();
	}
	
	public function getCadreValidationURL()
	{
		return 'http://jsp.binets.fr/cadreconfirmation?user='.$this->getUserConfirmationId();
	}
	
	public function sendValidationEmail()
	{
		$message = "Nous vous remercions d'avoir créé un compte sur notre site. Afin que vous puissiez vous connecter au plus vite, merci de confirmer votre adresse mail en cliquant sur le lien suivant :<br/>";
		$message .= '<a href="'.$this->getEmailValidationURL().'">'.$this->getEmailValidationURL().'</a><br/><br/>';
		$message .= 'En espérant vous revoir très bientôt sur notre site, cordialement,<br/>';
		$message .= 'Les administrateurs du site <a href="http://jsp.binets.fr">JSP</a>';
		$headers  = 'From: Binet JSP <jsp@binets.polytechnique.fr>' . "\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		mail($this->getEmail(),"[JSP] Confirmez votre adresse mail",$message,$headers);
	}
	
	public function sendValidationEmailForCadre()
	{
		$message  = "Nous vous remercions d'avoir créé un compte sur notre site. Afin que vous puissiez vous connecter au plus vite, merci de confirmer votre adresse mail en cliquant sur le lien suivant :<br/>";
		$message .= '<a href="'.$this->getEmailValidationURL().'">'.$this->getEmailValidationURL().'</a><br/><br/>';
		$message .= "Vous avez également demandé à être identifié comme un cadre de l'École polytechnique, votre demande est en cours de validation. Vous recevrez un email dès que celle-ci aura été validée, vous pourrez alors accéder à l'ensemble des évènements organisés par le binet JSP (pour le moment vous ne pouvez vous inscrire qu'à la semaine du réveillon).<br/><br/>";
		$message .= 'En espérant vous revoir très bientôt sur notre site, cordialement,<br/>';
		$message .= 'Les administrateurs du site <a href="http://jsp.binets.fr">JSP</a>';
		$headers  = 'From: Binet JSP <jsp@binets.polytechnique.fr>' . "\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		mail($this->getEmail(),"[JSP] Confirmez votre adresse mail",$message,$headers);
		
		$message  = "Un utilisateur du site JSP demande à être validé en tant que cadre sur le site. Veuillez vérifier les infos suivantes et vous assurer qu'il s'agit bien d'un cadre de l'école, puis cliquer sur le lien situé en dessous.<br/><br/>";
		$message .= "Nom : ".$this->getFullName()."<br/>";
		$message .= "Adresse mail : ".$this->getEmail()."<br/><br/>";
		$message .= '<a href="'.$this->getCadreValidationURL().'">Je confirme qu\'il s\'agit bien d\'un cadre.</a><br/><br/>';
		$headers  = 'From: Binet JSP <jsp@binets.polytechnique.fr>' . "\r\n" .
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		mail('quentin.fiard@gmail.com',"[JSP] Demande de validation d'un cadre X",$message,$headers);
	}
	
	public function sendCadreXConfirmationEmail()
	{
		$message = "Les administrateurs du site JSP viennent de valider votre inscription en tant que cadre de l'École polytechnique. Vous pouvez à présent voir et vous inscrire au weekend JSP.<br/><br/>";
		$message .= 'En espérant vous revoir très bientôt sur notre site, cordialement,<br/>';
		$message .= 'Les administrateurs du site <a href="http://jsp.binets.fr">JSP</a>';
		$headers  = 'From: Binet JSP <jsp@binets.polytechnique.fr>' . "\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		mail($this->getEmail(),"[JSP] Validation de votre inscription en tant que cadre X",$message,$headers);
	}
	
	public function sendResetPasswordEmail($newPassword)
	{
		$message = 'Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte sur  <a href="http://jsp.binets.fr">le site des JSP</a>. Afin de vous permettre de choisir un nouveau mot de passe, un mot de passe temporaire a été défini pour vous permettre de vous connecter à la page de gestion de votre compte.<br/><br/>';
		$message .= 'Vos nouveaux identifiants de connexion sont :<br/><br/>';
		$message .= 'Adresse mail : '.$this->getEmail().'<br/>';
		$message .= 'Mot de passe : '.$newPassword.'<br/><br/>';
		$message .= 'En espérant vous revoir très bientôt sur notre site, cordialement,<br/>';
		$message .= 'Les administrateurs du site <a href="http://jsp.binets.fr">JSP</a>';
		$headers  = 'From: Binet JSP <jsp@binets.polytechnique.fr>' . "\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		mail($this->getEmail(),"[JSP] Réinitialisation de votre mot de passe",$message,$headers );
	}

	public function getValidated() {
		return $this->validated;
	}

	function isAdherentKes()
	{
		return $this->isCadreX || $this->isCotisant();
	}

	public function isCadreX()
	{
		return $this->isCadreX;
	}

	public function isCotisant() {
		return Database::shared()->isExternalUserCotisant($this);
	}
}

?>