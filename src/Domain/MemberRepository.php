<?php

namespace Domain;

use Doctrine\ORM\EntityRepository;

class MemberRepository extends EntityRepository {

    const DEFAULT_LIMIT = 20;

    public function getMembers($branch = null, int $page = 0, int $n = self::DEFAULT_LIMIT) {

        $qb = $this->getEntityManager()->createQueryBuilder();

        $where = $qb->expr()
            ->eq('m.confirmed', ':confirmed');

        $params = [
            'confirmed' => true,
        ];

        if ($branch &&
            \JSONObject::get('branches', $branch) !== false) {

            $where = $qb->expr()->andX(
                $qb->expr()
                    ->eq('m.branch', ':branch'),
                $where);
            $params = array_merge([
                'branch' => $branch
            ], $params);
        }

        return ($this->getEntityManager()
            ->createQueryBuilder()
            ->select('m')
            ->from(Member::class, 'm')
            ->where($where)
            ->orderBy('m.timestamp', 'DESC')
            ->getQuery()
            ->setParameters($params)
            ->setFirstResult($page * self::DEFAULT_LIMIT)
            ->setMaxResults($n)
        )->execute();
    }



}