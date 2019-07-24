<?php
namespace Concrete\Package\CommunityStoreReward\Src\CommunityStoreReward;

use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Support\Facade\DatabaseORM as dbORM;
use Concrete\Core\Entity\User\User;
use Concrete\Package\CommunityStore\Src\CommunityStore\Order\Order;

/**
 * @ORM\Entity
 * @ORM\Table(name="CommunityStoreRewardPoints")
 */


class Point {
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;


	/**
	 * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User", inversedBy="uID")
	 * @ORM\JoinColumn(name="uID", referencedColumnName="uID")
	 */
	protected $uID;


	/**
	 * @ORM\Column(type="string",length=100,nullable=false)
	 */
	public $email;


	/**
	 * @ORM\ManyToOne(targetEntity="\Concrete\Package\CommunityStore\Src\CommunityStore\Order\Order", inversedBy="oID")
	 * @ORM\JoinColumn(name="oID", referencedColumnName="oID")
	 */
	protected $oID;


	/**
	 * @ORM\Column(type="string",length=100,nullable=true)
	 */
	public $discountCode;


	/**
	 * @ORM\Column(type="datetime",nullable=true)
	 */
	protected $date;


	/**
	 * * @ORM\Column(type="integer",length=6,nullable=true)
	 */
	protected $points;


	/**
	 * @return Point
	 */
	public static function getByID ($pID) {
		$em = dbORM::entityManager();

		return $em->find(get_class(), $pID);
	}


	public function save () {
		$em = dbORM::entityManager();
		$em->persist($this);
		$em->flush();
	}

	public function delete () {
		$em = dbORM::entityManager();
		$em->remove($this);
		$em->flush();
	}

	public function getID () {
		return $this->id;
	}

	/**
	 * @param User $user
	 */
	public function setUser(User $user){
		$this->uID = $user;
	}

	/**
	 * @return Order|null
	 */
	public function getOID () {
		return $this->oID;
	}

	/**
	 * @param Order $order
	 */
	public function setOID(Order $order){
		$this->oID = $order;
	}

	/**
	 * @return User|null
	 */
	public function getUser(){
		return $this->uID;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setEmail($email){
		$this->email = $email;
	}

	public function getDiscountCode() {
		return $this->discountCode;
	}

	public function setDiscountCode($code){
		$this->discountCode = $code;
	}

	public function setDate($date){
		$this->date = $date;
	}

	/**
	 * @return \DateTime|null
	 */
	public function getDate(){
		return $this->date;
	}


	public function getPoints () {
		return $this->points;
	}

	public function setPoints ($points) {
		$this->points = $points;
	}
}