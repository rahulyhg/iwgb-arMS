<?php

namespace Domain;

/**
 * @Table(name="members")
 * @Entity
 */
class Member {
    /**
     * @var string
     *
     * @Column(name="id", type="string", length=13, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(name="json", type="text", length=0, nullable=false)
     */
    private $json;

    /**
     * @var \DateTime
     *
     * @Column(name="timestamp", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $timestamp = 'CURRENT_TIMESTAMP';

    /**
     * @var bool
     *
     * @Column(name="confirmed", type="boolean", nullable=false)
     */
    private $confirmed = '0';

    /**
     * @var string
     *
     * @Column(name="branch", type="string", length=30, nullable=false)
     */
    private $branch;

    /**
     * @var string
     *
     * @Column(name="membership", type="string", length=50, nullable=false)
     */
    private $membership;


}
