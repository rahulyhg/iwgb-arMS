<?php

namespace Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * src/Domain/Verify
 *
 * @ORM\Table(name="verify", indexes={@ORM\Index(name="id", columns={"id"})})
 * @ORM\Entity
 */
class VerificationKey {
    /**
     * @var int
     *
     * @ORM\Column(name="incr", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $incr;

    /**
     * @var Member
     *
     * @ORM\ManyToOne(targetEntity="Member", inversedBy="verificationKeys")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="member", referencedColumnName="id")
     * })
     */
    private $member;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=10, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="keystr", type="string", length=23, nullable=false)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="\Domain\AuthKeyGenerator")
     */
    private $key;

    /**
     * @var bool
     *
     * @ORM\Column(name="verified", type="boolean", nullable=false)
     */
    private $verified = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $timestamp = 'CURRENT_TIMESTAMP';

    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isVerified(): bool {
        return $this->verified;
    }

    /**
     * @param bool $verified
     */
    public function setVerified(bool $verified): void {
        $this->verified = $verified;
    }

    /**
     * @return int
     */
    public function getIncr(): int {
        return $this->incr;
    }

    /**
     * @return Member
     */
    public function getMember(): Member {
        return $this->member;
    }

    /**
     * @return string
     */
    public function getKey(): string {
        return $this->key;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp(): \DateTime {
        return $this->timestamp;
    }




}
