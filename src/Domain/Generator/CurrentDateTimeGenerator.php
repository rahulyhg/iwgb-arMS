<?php

namespace Domain\Generator;

class CurrentDateTimeGenerator extends \Doctrine\ORM\Id\AbstractIdGenerator {

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function generate(\Doctrine\ORM\EntityManager $em, $entity) {
        return (new \DateTime())->format(\DateTime::ATOM);
    }
}