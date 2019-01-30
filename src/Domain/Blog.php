<?php

namespace Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="blogs", indexes={
 *     @ORM\Index(name="admin", columns={"admin"})
 * })
 * @ORM\Entity
 */
class Blog {
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     * @ORM\Id
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="friendly_name", type="string", length=100, nullable=false)
     */
    private $friendlyName;

    /**
     * @var string
     *
     * @ORM\Column(name="friendly_singular", type="string", length=100, nullable=false)
     */
    private $friendlySingular;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=10, nullable=false)
     */
    private $type;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="admin", referencedColumnName="email")
     * })
     */
    private $admin;

    /**
     * Blog constructor.
     * @param string $name
     * @param string $friendlyName
     * @param string $friendlySingular
     * @param string $type
     * @param User $admin
     */
    public function __construct(string $name, string $friendlyName, string $friendlySingular, string $type, User $admin) {
        $this->name = $name;
        $this->friendlyName = $friendlyName;
        $this->friendlySingular = $friendlySingular;
        $this->type = $type;
        $this->admin = $admin;
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
    public function setName(string $name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getFriendlyName(): string {
        return $this->friendlyName;
    }

    /**
     * @param string $friendlyName
     */
    public function setFriendlyName(string $friendlyName) {
        $this->friendlyName = $friendlyName;
    }

    /**
     * @return string
     */
    public function getFriendlySingular(): string {
        return $this->friendlySingular;
    }

    /**
     * @param string $friendlySingular
     */
    public function setFriendlySingular(string $friendlySingular) {
        $this->friendlySingular = $friendlySingular;
    }

    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type) {
        $this->type = $type;
    }

    /**
     * @return User
     */
    public function getAdmin(): User {
        return $this->admin;
    }

    /**
     * @param User $admin
     */
    public function setAdmin(User $admin) {
        $this->admin = $admin;
    }
}
