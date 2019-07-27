<?php

namespace Concrete\Package\CommunityStoreReward;

use Concrete\Core\Package\Package;
use Concrete\Package\CommunityStore\Src\CommunityStore\Discount\DiscountRule;
use Whoops\Exception\ErrorException;
use Concrete\Core\Support\Facade\Events;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Page\Page;
use Concrete\Core\Package\PackageService;
use Concrete\Core\User\UserInfo;
use Concrete\Package\CommunityStoreReward\Src\CommunityStoreReward\Point;
use Concrete\Package\CommunityStore\Src\CommunityStore\Discount\DiscountCode;
use Concrete\Package\CommunityStoreReward\Src\CommunityStoreReward\Code;
use Config;
use Concrete\Core\Logging\Logger;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends Package {
	protected $pkgHandle = 'community_store_reward';
	protected $appVersionRequired = '8.2.1'; /* Matches community_store */
	protected $pkgVersion = '0.1';

	protected $pkgAutoloaderRegistries = [
		'src/CommunityStoreReward' => '\Concrete\Package\CommunityStoreReward\Src\CommunityStoreReward',
	];

	public function getPackageDescription () {
		return t("Reward points for Community Store");
	}

	public function getPackageName () {
		return t("Reward Points");
	}

	public function install () {
		$installed = $this->app->make(PackageService::class)->getInstalledHandles();
		if (!(is_array($installed) && in_array('community_store', $installed))) {
			throw new ErrorException(t('This package requires that Community Store be installed'));
		} else {
			$pkg = parent::install();
		}
		$this->singlePages($pkg);
	}

	public function upgrade () {
		$pkg = $this->app->make(PackageService::class)->getByHandle($this->pkgHandle);
		$this->singlePages($pkg);
	}

	private function singlePages ($pkg) {
		$this->installSinglePage('/dashboard/store/rewards', $pkg);
	}

	private function installSinglePage ($path, $pkg) {
		$page = Page::getByPath($path);
		if (!is_object($page) || $page->isError()) {
			SinglePage::add($path, $pkg);
		}
	}

	public function on_start () {
		Events::addListener('on_user_validate', function ($event) {
			/* @var $event \Concrete\Core\User\Event\UserInfo */
			$ui = $event->getUserInfoObject();
		});


		Events::addListener('on_user_add', function ($event) {
			/* @var $event \Concrete\Core\User\Event\UserInfoWithPassword */
			$ui = $event->getUserInfoObject();

			if (Config::get('community_store_rewards.register')) {
				$points = Config::get('community_store_rewards.register_points');
				if ($points) {
					$this->addPoints($ui, $points);
				}
			}
		});


		Events::addListener('on_community_store_payment_complete', function ($event) {
			/* @var $event \Concrete\Package\CommunityStore\Src\CommunityStore\Order\OrderEvent */
			$order = $event->getOrder();

		});
	}

	/**
	 * @param $arg string|UserInfo
	 * @param $points integer
	 */
	private function addPoints ($arg, $points) {
		$point = new Point();
		if ($arg instanceof UserInfo) {
			$point->setUser($arg->getEntityObject());
		} else {
			$point->setEmail($arg);
		}
		$point->setPoints($points);
		$point->save();

		$this->assignCode($arg);
	}

	/**
	 * @param $arg string|UserInfo
	 * @param $points integer
	 */
	private function assignCode ($arg) {
		// First we get the points and see if we have enough to assign

		if ($arg instanceof UserInfo) {
			$totalPoints = Point::getTotalByUserID($arg->getUserID());
		} else {
			$totalPoints = Point::getTotalByEmail($arg);
		}

		if ($totalPoints <= 0)
			return false;

		$issue = Config::get('community_store_rewards.issue_points_total');
		if ($issue <= 0)
			return false;

		if ($totalPoints < $issue) {
			return false;
		}

		// Woot!, we have enough points - award the code
		$rule = DiscountRule::getByID(Config::get('community_store_rewards.discount_rule_id'));
		if (!$rule) {
			$log = new Logger('rewards');
			$log->addCritical('Discount rule has been removed');
			return false;
		}

		$discountCode = new DiscountCode();
		// Generate a new unique Code
		// Currently no provision for restricting to a specific user, so we need an element of obfuscation
		// to discourage trial and error attempts

		// TODO make prefix and length user definable
		$random = 'VIP'.strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

		$discountCode->setCode($random);
		$discountCode->setDateAdded(new \DateTime());
		$discountCode->setDiscountRule($rule);
		$discountCode->save();

		$rewardCode = new Code();
		$rewardCode->setDiscountCode($discountCode);
		if ($arg instanceof UserInfo) {
			$rewardCode->setUser($arg->getEntityObject());
		} else {
			$rewardCode->setEmail($arg);
		}
		$rewardCode->save();

		// Take the points offf
		$point = new Point();
		if ($arg instanceof UserInfo) {
			$point->setUser($arg->getEntityObject());
		} else {
			$point->setEmail($arg);
		}
		$point->setPoints(-1 * $issue);
		$point->setDiscountCode($rewardCode);
		$point->save();

		// TODO Send the email

	}
}