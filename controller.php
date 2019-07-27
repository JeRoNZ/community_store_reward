<?php

namespace Concrete\Package\CommunityStoreReward;

use Concrete\Core\Package\Package;
use Whoops\Exception\ErrorException;
use Concrete\Core\Support\Facade\Events;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\Page\Page;
use Concrete\Core\Package\PackageService;

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

	public function on_start ()
	{
		Events::addListener('on_user_validate', function ($event) {
			/* @var $event \Concrete\Core\User\Event\UserInfo */
			$user = $event->getUserInfoObject();
		});


		Events::addListener('on_user_add', function ($event) {
			/* @var $event \Concrete\Core\User\Event\UserInfoWithPassword */
			$user = $event->getUserInfoObject();
		});


		Events::addListener('on_community_store_payment_complete', function ($event) {
			/* @var $event \Concrete\Package\CommunityStore\Src\CommunityStore\Order\OrderEvent */
			$order = $event->getOrder();

		});
	}
}