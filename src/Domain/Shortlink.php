<?php

namespace Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * src/Domain/Verify
 *
 * @ORM\Table(name="shortlinks", indexes={@ORM\Index(name="id", columns={"id"})})
 * @ORM\Entity
 */
class Shortlink {

    /**
    * @var string
    *
    * @ORM\Column(name="id", type="string", nullable=false)
    * @ORM\Id
    */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="creator", referencedColumnName="email")
     * })
     */
    private $creator;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=200, nullable=false)
     */
    private $url;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="protected", type="boolean", nullable=false)
     */
    private $protected = false;

    /**
     * Shortlink constructor.
     * @param User $creator
     * @param string $url
     * @param bool $protected
     */
    public function __construct(User $creator, string $url, bool $protected) {
        $this->creator = $creator;
        $this->url = $url;
        $this->protected = $protected;
    }

    /**
     * @return User
     */
    public function getCreator(): User {
        return $this->creator;
    }

    /**
     * @param User $creator
     */
    public function setCreator(User $creator): void {
        $this->creator = $creator;
    }

    /**
     * @return string
     */
    public function getUrl(): string {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void {
        $this->url = $url;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void {
        $this->enabled = $enabled;
    }

    /**
     * @return bool
     */
    public function isProtected(): bool {
        return $this->protected;
    }

    /**
     * @param bool $protected
     */
    public function setProtected(bool $protected): void {
        $this->protected = $protected;
    }

    /**
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void {
        $this->id = $id;
    }




}