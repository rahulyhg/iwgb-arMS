<?php

namespace Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class User {
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=false)
     * @ORM\Id
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="pass", type="string", length=255, nullable=false)
     */
    private $pass;

    /**
     * @var string
     *
     * @ORM\Column(name="permissions", type="string", length=20, nullable=false)
     */
    private $permissions;

    /**
     * @var string
     *
     * @ORM\Column(name="organisation", type="string", length=20, nullable=false)
     */
    private $organisation;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="photo_id", type="string", length=13, nullable=false)
     */
    private $photoId;

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
     * @var bool
     *
     * @ORM\Column(name="membership_admin", type="boolean", nullable=false)
     */
    private $membershipAdministrator = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="contributor", type="boolean", nullable=false)
     */
    private $contributor = false;

    /**
     * User constructor.
     * @param string $email
     * @param string $pass
     * @param string $permissions
     * @param string $organisation
     * @param string $photoId
     * @param string $firstName
     * @param string $surname
     */
    public function __construct(string $email, string $pass, string $permissions, string $organisation, string $photoId, string $firstName, string $surname) {
        $this->email = $email;
        $this->pass = password_hash($pass, PASSWORD_DEFAULT);
        $this->permissions = $permissions;
        $this->organisation = $organisation;
        $this->firstName = $firstName;
        $this->surname = $surname;
        $this->photoId = $photoId;

        $this->name = $this->firstName . $this->surname;
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
    public function getPass(): string {
        return $this->pass;
    }

    /**
     * @param string $pass
     */
    public function setPass(string $pass): void {
        $this->pass = password_hash($pass, PASSWORD_DEFAULT);
    }

    /**
     * @return string
     */
    public function getPermissions(): string {
        return $this->permissions;
    }

    /**
     * @param string $permissions
     */
    public function setPermissions(string $permissions): void {
        $this->permissions = $permissions;
    }

    /**
     * @return string
     */
    public function getOrganisation(): string {
        return $this->organisation;
    }

    /**
     * @param string $organisation
     */
    public function setOrganisation(string $organisation): void {
        $this->organisation = $organisation;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPhotoId(): string {
        return $this->photoId;
    }

    /**
     * @param string $photoId
     */
    public function setPhotoId(string $photoId): void {
        $this->photoId = $photoId;
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
     * @return bool
     */
    public function isMembershipAdministrator(): bool {
        return $this->membershipAdministrator;
    }

    /**
     * @param bool $membershipAdministrator
     */
    public function setMembershipAdministrator(bool $membershipAdministrator): void {
        $this->membershipAdministrator = $membershipAdministrator;
    }

    /**
     * @return bool
     */
    public function isContributor(): bool {
        return $this->contributor;
    }

    /**
     * @param bool $contributor
     */
    public function setContributor(bool $contributor): void {
        $this->contributor = $contributor;
    }

    /**
     * Verify that $pass matches the hash associated with this user.
     * If the password hash needs updating, it will do so.
     *
     * @param string $pass
     * @return bool
     */
    public function verifyPassword(string $pass): bool {
        $result = false;
        if (password_verify($pass, $this->pass)) {
            $result = true;
        }
        if (password_needs_rehash($this->pass, PASSWORD_DEFAULT)) {
            $this->pass = password_hash($pass, PASSWORD_DEFAULT);
        }

        return $result;
    }


}
