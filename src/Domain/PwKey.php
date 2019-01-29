<?php

namespace Domain;

/**
 * src/Domain/PwKeys
 *
 * @Table(name="pw_keys", indexes={@Index(name="email", columns={"email"})})
 * @Entity
 */
class PwKey {
    /**
     * @var string
     *
     * @Column(name="keystr", type="string", length=23, nullable=false)
     * @Id
     */
    private $keystr;

    /**
     * @var \DateTime
     *
     * @Column(name="timestamp", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $timestamp = 'CURRENT_TIMESTAMP';

    /**
     * @var User
     *
     * @ManyToOne(targetEntity="User")
     * @JoinColumns({
     *   @JoinColumn(name="email", referencedColumnName="email")
     * })
     */
    private $email;


}
