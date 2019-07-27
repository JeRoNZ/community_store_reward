<?php
namespace Concrete\Package\CommunityStoreReward\Src\CommunityStoreReward;

use Doctrine\ORM\Mapping as ORM;
use Concrete\Core\Support\Facade\DatabaseORM as dbORM;
use Concrete\Core\Entity\User\User;
use Concrete\Package\CommunityStore\Src\CommunityStore\Discount\DiscountCode;

/**
 * @ORM\Entity
 * @ORM\Table(name="CommunityStoreRewardCodes")
 */


class Code {
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $rcID;


	/**
	 * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User", inversedBy="uID")
	 * @ORM\JoinColumn(name="uID", referencedColumnName="uID")
	 */
	protected $uID;


	/**
	 * @ORM\Column(type="string",length=100,nullable=true)
	 */
	public $email;


	/**
	 * @ORM\OneToOne(targetEntity="\Concrete\Package\CommunityStore\Src\CommunityStore\Discount\DiscountCode", inversedBy="dcID")
	 * @ORM\JoinColumn(name="dcID", referencedColumnName="dcID")
	 */
	public $dcID;


	/**
	 * @return Code
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
	 * @return User|null
	 */
	public function getUserObject() {
		return $this->uID;
	}

	/**
	 * @return DiscountCode|null
	 */
	public function getDiscountCodeObject () {
		return $this->dcID;
	}

	/**
	 * @param DiscountCode $dc
	 */
	public function setDiscountCode(DiscountCode $dc){
		$this->dcID = $dc;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setEmail($email){
		$this->email = $email;
	}
}