<?php
namespace Concrete\Package\CommunityStoreReward\Src\CommunityStoreReward;

use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\ItemList\Database\ItemList;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Concrete\Package\CommunityStoreReward\Src\CommunityStoreReward\Code;

class CodeList extends ItemList
{
    protected $sortBy = "alpha";

    public function setSortBy($sort)
    {
        $this->sortBy = $sort;
    }

    public function createQuery()
    {
		$this->query
			->select('r.rcID')
			->from('CommunityStoreRewardCodes', 'r')
			->leftJoin('r', 'CommunityStoreDiscountCodes', 'd', 'r.dcID = d.dcID');
    }


    public function finalizeQuery(\Doctrine\DBAL\Query\QueryBuilder $query)
    {
        switch ($this->sortBy) {
            case "alpha":
                $query->orderBy('dcCode', 'ASC');
                break;
        }

        return $query;
    }

    public function getResult($queryRow)
    {
        return Code::getByID($queryRow['id']);
    }

    protected function createPaginationObject()
    {
        $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct r.rcID)')->setMaxResults(1);
        });
        $pagination = new Pagination($this, $adapter);

        return $pagination;
    }

    public function getTotalResults()
    {
        $query = $this->deliverQueryObject();

        return $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct r.rcID)')->setMaxResults(1)->execute()->fetchColumn();
    }

	/**
	 *
	 * @param mixed $email
	 */
	public function filterByEmail ($email) {
		$this->query->andWhere('r.email=:email');
		$this->query->setParameter('email', $email);
	}

	/**
	 * Filter by uID
	 *
	 * @param int $uID
	 */
	public function filterByUID ($uID) {
		$this->query->andWhere('r.uID=:uID');
		$this->query->setParameter('uID', $uID);
	}
}
