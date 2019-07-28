<?php
namespace Concrete\Package\CommunityStoreReward\Src\CommunityStoreReward;

use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\ItemList\Database\ItemList;
use Pagerfanta\Adapter\DoctrineDbalAdapter;

class PointList extends ItemList
{
    protected $sortBy = "alpha";

    public function setSortBy($sort)
    {
        $this->sortBy = $sort;
    }

    public function createQuery()
    {
		$this->query
			->select('r.id')
			->from('CommunityStoreRewardPoints', 'r')
			->leftJoin('r', 'CommunityStoreRewardCodes', 'c', 'r.rcID = c.rcID');
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
        return Point::getByID($queryRow['id']);
    }

    protected function createPaginationObject()
    {
        $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct r.id)')->setMaxResults(1);
        });
        $pagination = new Pagination($this, $adapter);

        return $pagination;
    }

    public function getTotalResults()
    {
        $query = $this->deliverQueryObject();

        return $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct r.id)')->setMaxResults(1)->execute()->fetchColumn();
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
