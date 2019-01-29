<?php

namespace Domain;

class UniqidGenerator extends \Doctrine\ORM\Id\AbstractIdGenerator {

    /**
     * @inheritdoc
     */
    public function generate(\Doctrine\ORM\EntityManager $em, $entity) {
        return uniqid();
    }
}