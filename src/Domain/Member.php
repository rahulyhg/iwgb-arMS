<?php

namespace Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="members")
 * @ORM\Entity
 */
class Member {
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=13, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="\Domain\Generator\UniqidGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="branchdata", type="text", length=0, nullable=false)
     */
    private $branchData;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @var bool
     *
     * @ORM\Column(name="confirmed", type="boolean", nullable=false, options={"default"="0"})
     */
    private $confirmed = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="branch", type="string", length=30, nullable=false)
     */
    private $branch;

    /**
     * @var string
     *
     * @ORM\Column(name="membership", type="string", length=50, nullable=false)
     */
    private $membership;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=100, nullable=false)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=100, nullable=false)
     */
    private $surname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dob", type="date", nullable=false)
     */
    private $dob;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=100, nullable=false)
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=14, nullable=false)
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=200, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=500, nullable=false)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="postcode", type="string", length=20, nullable=false)
     */
    private $postcode;

    /**
     * @var VerificationKey[]
     *
     * @ORM\OneToMany(targetEntity="VerificationKey", mappedBy="member")
     */
    private $verificationKeys;

    /**
     * Member constructor.
     * @param string $branchData
     * @param string $branch
     * @param string $membership
     * @param string $firstName
     * @param string $surname
     * @param int $dob
     * @param string $gender
     * @param string $mobile
     * @param string $email
     * @param string $address
     * @param string $postcode
     * @throws \Exception
     */
    public function __construct(string $branchData, string $branch, string $membership, string $firstName, string $surname, int $dob, string $gender, string $mobile, string $email, string $address, string $postcode) {
        $this->branchData = $branchData;
        $this->timestamp = new \DateTime();
        $this->branch = $branch;
        $this->membership = $membership;
        $this->firstName = $firstName;
        $this->surname = $surname;
        $this->dob = new \DateTime($dob);
        $this->gender = $gender;
        $this->mobile = $mobile;
        $this->email = $email;
        $this->address = $address;
        $this->postcode = $postcode;
        $this->verificationKeys = new ArrayCollection();
    }

    /**
     * @param $a
     * @return Member
     * @throws \Exception If $dob is invalid.
     */
    public static function constructFromData(array $a): self {
        return new self(
            json_encode($a['branchData']),
            $a['branch'],
            $a['membership'],
            $a['first-name'],
            $a['surname'],
            $a['dob'],
            $a['gender'],
            $a['mobile'],
            $a['email'],
            $a['address'],
            $a['postcode']
        );
    }

    /**
     * @return string
     */
    public function getBranchData(): string {
        return $this->branchData;
    }

    /**
     * @param string $branchData
     */
    public function setBranchData(string $branchData): void {
        $this->branchData = $branchData;
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool {
        return $this->confirmed;
    }

    /**
     * @param bool $confirmed
     */
    public function setConfirmed(bool $confirmed): void {
        $this->confirmed = $confirmed;
    }

    /**
     * @return string
     */
    public function getBranch(): string {
        return $this->branch;
    }

    /**
     * @param string $branch
     */
    public function setBranch(string $branch): void {
        $this->branch = $branch;
    }

    /**
     * @return string
     */
    public function getMembership(): string {
        return $this->membership;
    }

    /**
     * @param string $membership
     */
    public function setMembership(string $membership): void {
        $this->membership = $membership;
    }

    /**
     * @return string
     */
    public function getFirstName(): string {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getSurname(): string {
        return $this->surname;
    }

    /**
     * @param string $surname
     */
    public function setSurname(string $surname): void {
        $this->surname = $surname;
    }

    /**
     * @return \DateTime
     */
    public function getDob(): \DateTime {
        return $this->dob;
    }

    /**
     * @param \DateTime $dob
     */
    public function setDob(\DateTime $dob): void {
        $this->dob = $dob;
    }

    /**
     * @return string
     */
    public function getGender(): string {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender(string $gender): void {
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getMobile(): string {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile(string $mobile): void {
        $this->mobile = $mobile;
    }

    /**
     * @return string
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getAddress(): string {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getPostcode(): string {
        return $this->postcode;
    }

    /**
     * @param string $postcode
     */
    public function setPostcode(string $postcode): void {
        $this->postcode = $postcode;
    }

    /**
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp(): \DateTime {
        return $this->timestamp;
    }

    /**
     * @return VerificationKey[]
     */
    public function getVerificationKeys(): array {
        return $this->verificationKeys;
    }


    /**
     * @return bool Is this member's application verified
     */
    public function isVerified(): bool {
        $result = true;
        foreach ($this->getVerificationKeys() as $key) {
            if (!$key->isVerified()) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * @param string $k
     * @param string $t
     * @return bool was the provided verification key found
     */
    public function verify(string $k, string $t): bool {
        $found = false;
        foreach ($this->getVerificationKeys() as $key) {
            if ($key->getKey() == $k &&
                $key->getType() == $t) {
                $key->setVerified(true);
                $found = true;
            }
        }
        return $found;
    }

    public function getUnverifiedKeys() {
        $unverified = [];
        foreach ($this->getVerificationKeys() as $key) {
            if (!$this->isVerified()) {
                $unverified[] = $key;
            }
        }
        return $unverified;
    }

}
