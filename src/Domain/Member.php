<?php

namespace Domain;

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
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="json", type="text", length=0, nullable=false)
     */
    private $json;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $timestamp = 'CURRENT_TIMESTAMP';

    /**
     * @var bool
     *
     * @ORM\Column(name="confirmed", type="boolean", nullable=false)
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


}
