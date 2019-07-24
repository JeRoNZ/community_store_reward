<?php

namespace Concrete\Package\CommunityStoreReward;

use Package;
use Whoops\Exception\ErrorException;
use Concrete\Core\Support\Facade\Events;

defined('C5_EXECUTE') or die(_("Access Denied."));

class Controller extends Package
{
    protected $pkgHandle = 'community_store_reward';
    protected $appVersionRequired = '8.2.1'; /* Matches community_store */
    protected $pkgVersion = '0.1';

	protected $pkgAutoloaderRegistries = [
		'src/CommunityStoreReward' => '\Concrete\Package\CommunityStoreReward\Src\CommunityStoreReward',
	];

    public function getPackageDescription()
    {
        return t("Reward points for Community Store");
    }

    public function getPackageName()
    {
        return t("Reward Points");
    }

    public function install()
    {
        $installed = Package::getInstalledHandles();
        if(!(is_array($installed) && in_array('community_store',$installed)) ) {
            throw new ErrorException(t('This package requires that Community Store be installed'));
        } else {
            $pkg = parent::install();
        }

    }

	public function on_start() {
		Events::addListener('on_user_validate', function ($event) {
			/* @var $event \Concrete\Core\User\Event\UserInfo */
			$user = $event->getUserInfoObject();
		});


		Events::addListener('on_user_add', function ($event) {
			/* @var $event \Concrete\Core\User\Event\UserInfoWithPassword */
			$user = $event->getUserInfoObject();




		});









	}
}