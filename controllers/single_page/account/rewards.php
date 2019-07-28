<?php
namespace Concrete\Package\CommunityStoreReward\Controller\SinglePage\Account;
defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\User\User;
use Concrete\Package\CommunityStoreReward\Src\CommunityStoreReward\PointList;
use Concrete\Core\Search\Pagination\PaginationFactory;

defined("C5_EXECUTE") or die("Access Denied.");

class Rewards extends PageController {
	public function view () {
		$u = new User();
		$list = new PointList();
		$list->filterByUID($u->getUserID());
		$list->sortBy('date','DESC');

		$list->setItemsPerPage(10);
		$factory = new PaginationFactory(\Request::getInstance());
		$paginator = $factory->createPaginationObject($list);
		$pagination = $paginator->renderDefaultView();
		$this->set('points', $paginator->getCurrentPageResults());
		$this->set('pagination', $pagination);
		$this->set('paginator', $paginator);
		$this->set('list', $list);
	}
}