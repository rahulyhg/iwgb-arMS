<?php

namespace Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * src/Domain/PwKeys
 *
 * @ORM\Table(name="pw_keys", indexes={@ORM\Index(name="email", columns={"email"})})
 * @ORM\Entity
 */
class PwKey {
    /**
     * @var string
     *
     * @ORM\Column(name="keystr", type="string", length=23, nullable=false)
     * @ORM\Id
     */
    private $keystr;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $timestamp = 'CURRENT_TIMESTAMP';

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="email", referencedColumnName="email")
     * })
     */
    private $email;


}
