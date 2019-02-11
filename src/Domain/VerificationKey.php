<?php

namespace Domain;

use Doctrine\ORM\Mapping as ORM;
use Slim\Container;

/**
 * src/Domain/Verify
 *
 * @ORM\Table(name="verify", indexes={@ORM\Index(name="id", columns={"id"})})
 * @ORM\Entity
 */
class VerificationKey {

    const VERIFICATION_SMS = 'Your IWGB verification key is %key%.\n\nRef: %application%';

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

    public function __construct(\Sender $send, Member $member, string $type) {
        $this->member = $member;
        $this->type = $type;
        $this->send($send);
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

    public function send(\Sender $send, array $settings): void {
        switch ($this->type) {
            case \KeyType::SMS:
                $send->twilio->messages->create($this->member->getMobile(), [
                    'from' => $settings['twilio']['from'],
                    'body' => self::processVerificationBody(self::VERIFICATION_SMS, [
                        'application' => $this->member->getId(),
                        'key' => $this->getKey(),
                    ]),
                ]);
                break;
        }
    }

    private static function processVerificationBody(string $body, array $params): string {
        foreach ($params as $key => $value) {
            $body = str_replace("%$key%", $value, $body);
        }
        return $body;
    }



}
