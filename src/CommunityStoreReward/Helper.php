<?php

namespace Concrete\Package\CommunityStoreReward\Src\CommunityStoreReward;

use Concrete\Core\User\UserInfo;
use Concrete\Package\CommunityStoreReward\Src\CommunityStoreReward\Point as RewardPoint;
use Concrete\Package\CommunityStoreReward\Src\CommunityStoreReward\Code as RewardCode;
use Concrete\Package\CommunityStore\Src\CommunityStore\Discount\DiscountRule;
use Concrete\Package\CommunityStore\Src\CommunityStore\Discount\DiscountCode;
use Concrete\Core\Logging\Logger;
use Config;

class Helper {
	/**
	 * @param $arg string|UserInfo
	 * @param $points integer
	 */
	public function addPoints ($arg, $points) {
		$point = new RewardPoint();
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
		$log = new Logger('rewards');

		// First we get the points and see if we have enough to assign

		if ($arg instanceof UserInfo) {
			$totalPoints = RewardPoint::getTotalByUserID($arg->getUserID());
			$log->addInfo(__METHOD__.' userinfo object, total points='.$totalPoints);
		} else {
			$totalPoints = RewardPoint::getTotalByEmail($arg);
			$log->addInfo(__METHOD__.' email, total points='.$totalPoints);
		}

		if ($totalPoints <= 0)
			return false;

		$issue = Config::get('community_store_rewards.issue_points_total');
		if ($issue <= 0)
			return false;

		if ($totalPoints < $issue) {
			return false;
		}

		// TODO make this loop so that multiple codes are issued if there are enough points

		// Woot!, we have enough points - award the code
		$rule = DiscountRule::getByID(Config::get('community_store_rewards.discount_rule_id'));
		if (!$rule) {
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

		$rewardCode = new RewardCode();
		$rewardCode->setDiscountCode($discountCode);
		if ($arg instanceof UserInfo) {
			$rewardCode->setUser($arg->getEntityObject());
		} else {
			$rewardCode->setEmail($arg);
		}
		$rewardCode->save();

		// Take the points off
		$point = new RewardPoint();
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