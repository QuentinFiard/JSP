<?php

namespace pages\admin;

use structures\Event;

use exceptions\UserHasAlreadyPaid;

use exceptions\InvalidPrice;

use exceptions\NoSuchUser;

use exceptions\NoReservationForUser;

use structures\User;

use exceptions\OptionHasUsers;

use database\Database;

use structures\Option;

require_once 'classes/nav/AdminOnlyPage.php';

use nav\AdminOnlyPage;

require_once ('classes/utilities/Server.php');
require_once 'classes/structures/Option.php';
require_once 'classes/structures/User.php';

require_once 'classes/exceptions/NoSuchUser.php';
require_once 'classes/exceptions/InvalidPrice.php';
require_once 'classes/exceptions/NoReservationForUser.php';
require_once 'classes/exceptions/UserHasAlreadyPaid.php';

use \utilities\Server;

class UserPaymentPage extends \nav\AdminOnlyPage {
	private static $page = null;
	
	public static function getPage()
	{
		if(self::$page==null)
		{
			self::$page = new UserPaymentPage();
		}
		return self::$page;
	}
	
	public function handleAjaxRequest()
	{
		$res = array();
		$res['success']=false;
		if(isset($_POST['confirmPayment']))
		{
			if(!isset($_POST['confirmPrice']) || !isset($_POST['confirmCaution']))
			{
				$res['confirm_price_required']=true;
				return $res;
			}
			
			$user = User::userWithUserId($_POST['userId']);
			$event = Event::eventWithEventId($_POST['eventId']);
			if(!$event)
			{
				$res['no_such_event']=true;
				return $res;
			}
			$price = $_POST['price'];
			$caution = $_POST['caution'];
			
			try {
				Database::shared()->confirmPaymentForEventUserPriceAndCaution($event,$user,$price,$caution);
				$res['success'] = true;
				return $res;
			}
			catch(NoSuchUser $e)
			{
				$res['no_such_user']=true;
				return $res;
			}
			catch(NoReservationForUser $e)
			{
				$res['no_reservation']=true;
				return $res;
			}
			catch(InvalidPrice $e)
			{
				$res['invalid_price']=true;

				$res['price']=$event->priceForUser($user);
				$res['caution']=$event->cautionForUser($user);
				
				return $res;
			}
			catch(UserHasAlreadyPaid $e)
			{
				$res['already_paid']=true;				
				return $res;
			}
			return $res;
		}
		else if(isset($_POST['getUsers']))
		{
			if(!isset($_POST['eventId']) || Event::eventWithEventId($_POST['eventId'])==null)
			{
				$res['no_such_event'] = true;
				return $res;
			}
			$event = Event::eventWithEventId($_POST['eventId']);
			$users = Database::shared()->getUsersWithFilters($_POST);
			
			$properties = User::getPaymentProperties();

			$res['keys'] = $properties;

			$res['users'] = array();
			$res['price'] = array();
			$res['subvention'] = array();
			$res['caution'] = array();
			$res['hasPaid'] = array();
			$res['waitingList'] = array();
			$res['reservationId'] = array();
			
			foreach($users as $user)
			{
				$tmp = array();
				
				foreach($properties as $key)
				{
					$tmp[] = $user->getProperty($key);
				}
				
				$res['users'][] = $tmp;
				$res['price'][] = $event->priceForUser($user);
				$option = $user->getOptionWithNameLikeForEvent('subvention',$event);
				if($option)
				{
					$res['subvention'][] = -$option->getPriceForUser($user);
				}
				else
				{
					$res['subvention'][] = false;
				}
				$res['caution'][] = $event->cautionForUser($user);
				$res['hasPaid'][] = !$user->hasToPayForEvent($event);
				$res['waitingList'][] = $user->isOnWaitingListForEvent($event);
				$reservation = $user->getReservationForEvent($event);
				$res['reservationId'][] = $reservation['reservationId'];
			}
			
			$res['success'] = true;
			return $res;
		}
		return parent::handleAjaxRequest();
	}
	
	public function __construct($path=null,$title=null,$event=null)
	{
		parent::__construct("payments","JSP - Page d'administration des paiements");
	}
}

?>