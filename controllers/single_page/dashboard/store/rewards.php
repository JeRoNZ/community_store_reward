<?php
namespace Concrete\Package\CommunityStoreReward\Controller\SinglePage\Dashboard\Store;


use Concrete\Core\Support\Facade\Config;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Package\CommunityStore\Src\CommunityStore\Discount\DiscountRuleList;
use Concrete\Package\CommunityStore\Src\CommunityStore\Discount\DiscountRule;
use Concrete\Core\Routing\Redirect;

class Rewards extends DashboardPageController {
	public function view () {
		// Load up discount rules

		$list = new DiscountRuleList();
		// Filter only the single use code rules
		$list->filter('drTrigger', 'code');
		$list->filter('drSingleUseCodes', '1');

		$results = $list->getResults();
		$rules = [];
		foreach ($results as $r) {
			/* @var $r DiscountRule */
			$rules[$r->getID()] = h($r->getName() . ' - ' . $r->getFullDisplay());
		}
		$this->set('rules', $rules);
	}

	public function validate($args)
	{
		$e = $this->app->make('helper/validation/error');
		$nv = $this->app->make('helper/validation/numbers');

		return $e;
	}


	public function save()
	{
		$this->view();
		$args = $this->request->request->all();

		if ($args && $this->token->validate('community_store')) {
			$errors = $this->validate($args);
			$this->error = $errors;

			if (!$errors->has()) {
				Config::save('community_store_rewards.discount_rule_id', $args['rule']);
				Config::save('community_store_rewards.spent_amount', $args['spent']);
				Config::save('community_store_rewards.points_awarded', $args['points']);
				Config::save('community_store_rewards.issue_points_total', $args['issue']);
				Config::save('community_store_rewards.emailalerts', $args['emailAlert']);
				Config::save('community_store_rewards.emailalertsname', $args['emailAlertName']);
				Config::save('community_store_rewards.rewardHeader', $args['receiptHeader']);
				Config::save('community_store_rewards.rewardFooter', $args['receiptFooter']);

				$this->flash('success', t('Settings Saved'));

				return Redirect::to('/dashboard/store/rewards');
			}
		}
	}
}