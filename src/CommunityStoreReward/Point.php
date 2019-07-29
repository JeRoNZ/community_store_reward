<?php
namespace Concrete\Package\CommunityStoreReward\Src\CommunityStoreReward;

use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Support\Facade\DatabaseORM as dbORM;
use Concrete\Core\Entity\User\User;
use Concrete\Package\CommunityStore\Src\CommunityStore\Order\Order;
use Database;

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
	 * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
	 * @ORM\JoinColumn(name="uID", referencedColumnName="uID",nullable=true)
	 */
	protected $uID;


	/**
	 * @ORM\Column(type="string",length=100,nullable=true)
	 */
	public $email;


	/**
	 * @ORM\OneToOne(targetEntity="\Concrete\Package\CommunityStoreReward\Src\CommunityStoreReward\Code")
	 * @ORM\JoinColumn(name="rcID", referencedColumnName="rcID",nullable=true)
	 */
	public $rcID;


	/**
	 * @ORM\OneToOne(targetEntity="\Concrete\Package\CommunityStore\Src\CommunityStore\Order\Order")
	 * @ORM\JoinColumn(name="oID", referencedColumnName="oID",nullable=true)
	 */
	public $oID;


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

	public function __construct(){
		$this->date = new \DateTime();
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
	 * @param \Concrete\Core\Entity\User\User $user
	 */
	public function setUser(User $user){
		$this->uID = $user;
	}

	/**
	 * @return \Concrete\Core\Entity\User\User|null
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

	/**
	 * @return Code|null
	 */
	public function getRewardCodeObject() {
		return $this->rcID;
	}

	/**
	 * @param $code Code
	 */
	public function setDiscountCode($code){
		$this->rcID = $code;
	}

	/**
	 * @return Order|null
	 */
	public function getOrderObject() {
		return $this->oID;
	}

	/**
	 * @param $order Order
	 */
	public function setOrderID($order){
		$this->oID = $order;
	}

	/**
	 * @param $date \DateTime
	 */
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

	/**
	 * @param $points integer
	 */
	public function setPoints ($points) {
		$this->points = $points;
	}

	/**
	 * @param $uID int
	 * @return int
	 */
	public static function getTotalByUserID ($uID) {
		$db = Database::Connection();
/* @var $db \Concrete\Core\Database\Connection\Connection*/

		return (int) $db->fetchColumn('SELECT SUM(points) FROM CommunityStoreRewardPoints WHERE uID=? GROUP BY uID', [$uID]);
	}

	/**
	 * @param $email string
	 * @return int
	 */
	public static function getTotalByEmail ($email) {
		$db = Database::Connection();
		/* @var $db \Concrete\Core\Database\Connection\Connection*/

		return (int) $db->fetchColumn('SELECT SUM(points) FROM CommunityStoreRewardPoints WHERE email=? GROUP BY email', [$email]);
	}
}