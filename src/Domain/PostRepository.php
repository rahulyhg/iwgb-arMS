<?php

namespace Domain;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

class PostRepository extends EntityRepository {

    const DEFAULT_LIM = 10;

    public function getPosts($where, $params = [], $n = self::DEFAULT_LIM) {
        return ($this->getEntityManager()
            ->createQueryBuilder()
            ->select('p')
            ->from('\Domain\Post', 'p')
            ->innerJoin('p.blog', 'b', Join::WITH)
            ->where($where)
            ->orderBy('p.timestamp', 'DESC')
            ->getQuery()
            ->setParameters($params)
            ->setMaxResults($n)
        )->execute();
    }

    public function getPostsByType(string $type, $where = true, $params = [], $n = self::DEFAULT_LIM) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $this->getPosts($qb->expr()->andX(
            $this->getEntityManager()
                ->createQueryBuilder()
                ->expr()
                ->eq('b.type', ':type'),
                $where),
            array_merge($params, ['type' => $type,]),
            $n);
    }

    public function getPinnedPost() {
        return $this->getPostsByType(\BlogType::Posts,
            $this->getEntityManager()
                ->createQueryBuilder()
                ->expr()
                ->neq('p.headerImage', ':noHeader'),
            ['noHeader' => '""'],
            1);
    }

    public function getStoriesExcluding(Post $post, $n = self::DEFAULT_LIM) {
        return $this->getPostsByType(\BlogType::Posts,
            $this->getEntityManager()->createQueryBuilder()->expr()
                ->neq('p.id', ':exclude'),
            ['exclude' => $post->getId()],
            $n);
    }
}