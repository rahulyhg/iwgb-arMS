<?php

namespace Domain;

/**
 * src/Domain/Verify
 *
 * @ORM\Table(name="verify", indexes={@ORM\Index(name="id", columns={"id"})})
 * @ORM\Entity
 */
class Verify {
    /**
     * @var int
     *
     * @ORM\Column(name="incr", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $incr;

    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=13, nullable=false)
     */
    private $id;

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
     */
    private $keystr;

    /**
     * @var string
     *
     * @ORM\Column(name="verified", type="string", length=10, nullable=false, options={"default"="no"})
     */
    private $verified = 'no';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $timestamp = 'CURRENT_TIMESTAMP';


}
