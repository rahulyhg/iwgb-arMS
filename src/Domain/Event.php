<?php

namespace Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="events")
 * @ORM\Entity
 */
class Event {
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=13, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="\Domain\UniqidGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50, nullable=false)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="who", type="string", length=200, nullable=true)
     */
    private $who;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="string", length=65535, nullable=true)
     */
    private $data;

    /**
     * Event constructor.
     * @param string $type
     * @param string $who
     * @param string $data
     * @throws \Exception
     */
    public function __construct(string $type, string $who = null, string $data = null) {
        $this->type = $type;
        $this->timestamp = new \DateTime();
        $this->who = $who;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp(): \DateTime {
        return $this->timestamp;
    }

    public function getStringTimestamp(): string {
        return $this->timestamp->format('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    public function getWho(): string {
        return $this->who;
    }

    /**
     * @return string
     */
    public function getData(): string {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData(string $data): void {
        $this->data = $data;
    }


}
