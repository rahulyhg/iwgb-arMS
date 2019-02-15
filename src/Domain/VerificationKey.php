<?php

namespace Domain;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Slim\Container;

/**
 * src/Domain/Verify
 *
 * @ORM\Table(name="verify", indexes={@ORM\Index(name="id", columns={"id"})})
 * @ORM\Entity
 */
class VerificationKey {

    const VERIFICATION_SMS = 'Your IWGB verification code is %key%';

    const VERIFICATION_EMAIL_SUBJECT = 'IWGB Verification';

    const VERIFICATION_EMAIL_HTML = [
        'content' => [
            'before' => [
                'Hey there,',
                'Your IWGB verification key is:',
                '**%key%**',
                'Or click the magic link below.',
            ],
            'after' => [
                '— Your friends at the IWGB',
            ],
            'footer' => [
                'This email was sent to enable you to verify your identity at [iwgb.org.uk](https://iwgb.org.uk).',
            ],
        ],
        'action' => [
            'href' => 'https://iwgb.org.uk/auth/verify/%id%?token=%token%',
            'display' => 'Verify',
        ],
    ];

    const VERIFICATION_EMAIL_TEXT = "Hey there,\r\n\r\nYour IWGB verification key is:\r\n\r\n%key%\r\n\r\nThanks!\r\n\r\n— Your friends at the IWGB";

    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="\Domain\UniqidGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=36, nullable=false)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="secret", type="string", length=36, nullable=false)
     */
    private $secret;

    /**
     * @var string
     *
     * @ORM\Column(name="keystr", type="string", length=23, nullable=false)
     */
    private $key;

    /**
     * @var bool
     *
     * @ORM\Column(name="verified", type="boolean", nullable=false)
     */
    private $verified = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="callback", type="string", nullable=false)
     */
    private $callback;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * VerificationKey constructor.
     * @param string $callback
     * @throws \Exception
     */
    public function __construct(string $callback) {
        $this->id = uniqid();
        $this->key = self::generateKey();
        $this->token = (Uuid::uuid1())->toString();
        $this->secret = (Uuid::uuid4())->toString();
        $this->callback = $callback;
        $this->timestamp = new \DateTime();
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
     * @return string
     */
    public function getCallback(): string {
        return $this->callback;
    }

    /**
     * @param string $callback
     */
    public function setCallback(string $callback): void {
        $this->callback = $callback;
    }

    /**
     * @return int
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getToken(): string {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getKey(): string {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getSecret(): string {
        return $this->secret;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp(): \DateTime {
        return $this->timestamp;
    }

    /**
     * @param \Sender $send
     * @param string $type
     * @param string $contact
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function send(\Sender $send, string $type, string $contact): void {
        switch ($type) {
            case \KeyType::SMS:
                $send->twilio->messages->create($contact, [
                    'from' => $send->twilioSettings['from'],
                    'body' => self::processVerificationBody(self::VERIFICATION_SMS, [
                        'key' => $this->getKey(),
                    ]),
                ]);
                break;
            case \KeyType::Email:
                $send->email->transactional($contact,
                    self::VERIFICATION_EMAIL_SUBJECT,
                    self::VERIFICATION_EMAIL_TEXT,
                    self::VERIFICATION_EMAIL_HTML,
                    [
                        'key' => $this->getKey(),
                        'token' => $this->getToken(),
                    ]);
                break;
        }
    }

    public function getLink(): string {
        return '/auth/verify/' . $this->getId() . '?token=' . $this->getToken();
    }

    private static function processVerificationBody(string $body, array $params): string {
        foreach ($params as $key => $value) {
            $body = str_replace("%$key%", $value, $body);
        }
        return $body;
    }

    private static function generateKey(): int {
        return rand(111111, 999999);
    }




}
