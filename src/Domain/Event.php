<?php

namespace Domain;

/**
 * @Table(name="events")
 * @Entity
 */
class Event {
    /**
     * @var string
     *
     * @Column(name="id", type="string", length=13, nullable=false)
     * @Id
     * @GeneratedValue(strategy="CUSTOM")
     * @CustomIdGenerator(class="UniqidGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(name="type", type="string", length=50, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @Column(name="act_upon", type="string", length=100, nullable=false)
     */
    private $actUpon;

    /**
     * @var string
     *
     * @Column(name="act_by", type="string", length=50, nullable=false)
     */
    private $actBy;

    /**
     * @var \DateTime
     *
     * @Column(name="timestamp", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $timestamp = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @Column(name="notes", type="text", length=0, nullable=false)
     */
    private $notes;


}
