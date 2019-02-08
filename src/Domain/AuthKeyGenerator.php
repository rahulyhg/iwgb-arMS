<?php
/**
 * Created by PhpStorm.
 * User: hello
 * Date: 08/02/2019
 * Time: 16:22
 */

namespace Domain;

class AuthKeyGenerator extends \Doctrine\ORM\Id\AbstractIdGenerator {

    /**
     * {@inheritdoc}
     */
    public function generate(\Doctrine\ORM\EntityManager $em, $entity) {
        return rand(1111, 9999);
    }
}