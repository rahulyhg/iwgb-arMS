<?php

namespace Domain;

use Doctrine\ORM\EntityRepository;
use Exception;
use JSONObject;
use League\Csv\Writer;

class MemberRepository extends EntityRepository {

    const DEFAULT_LIMIT = 20;

    /**
     * {@inheritdoc}
     */
    public function find($id, $lockMode = null, $lockVersion = null, $unconfirmed = false) {
        try {
            /** @var Member $member */
            $member = parent::find($id, $lockMode, $lockVersion);
        } catch (Exception $e) {
            return null;
        }
        if (!$unconfirmed &&
            !$member->isConfirmed()) {
            return null;
        }
        return $member;
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $unconfirmed = false) {
        try {
            $members = parent::findBy($criteria, $orderBy, $limit, $offset);
        } catch (Exception $e) {
            return null;
        }

        $results = [];

        if (!empty($members)) {
            foreach ($members as $member) {
                /** @var Member $member */
                if ($unconfirmed &&
                    !$member->isConfirmed()) {
                    continue;
                }
                $results[] = $member;
            }
        } else {
            return null;
        }
        return $results;

    }

    public function getMembers($branch = null,
                               int $page = 0,
                               string $sort = 'timestamp',
                               string $order = 'asc',
                               bool $confirmed = false,
                               bool $unverified = false,
                               int $n = self::DEFAULT_LIMIT) {

        $qb = $this->getEntityManager()->createQueryBuilder();

        $where = $qb->expr()
            ->eq('m.verified', ':verified');

        $params = [
            'verified' => !$unverified,
        ];

        if ($branch &&
            JSONObject::get('branches', $branch) !== false) {

            $where = $qb->expr()->andX(
                $qb->expr()
                    ->eq('m.branch', ':branch'),
                $where);
            $params = array_merge([
                'branch' => $branch,
            ], $params);
        }

        if ($confirmed) {
            $where = $qb->expr()->andX(
                $qb->expr()
                    ->eq('m.confirmed', ':confirmed'),
                $where);
            $params = array_merge([
                'confirmed' => true,
            ], $params);
        }

        return ($this->getEntityManager()
            ->createQueryBuilder()
            ->select('m')
            ->from(Member::class, 'm')
            ->where($where)
            ->orderBy("m.$sort", $order)
            ->getQuery()
            ->setParameters($params)
            ->setFirstResult($page * self::DEFAULT_LIMIT)
            ->setMaxResults($n)
        )->execute();
    }

    /**
     * @param Member[] $members
     * @return array
     */
    public static function toArray(array $members): array {
        $a = [];
        foreach ($members as $member) {
            $a[] = $member->toArray();
        }
        return $a;
    }

    /**
     * @param Member[] $members
     * @return string
     * @throws \League\Csv\CannotInsertRecord
     */
    public static function toCsv(array $members): string {
        $csv = Writer::createFromFileObject(new \SplTempFileObject());
        $csv->insertOne(array_keys($members[0]->toArray()));
        $csv->insertAll(self::toArray($members));
        return $csv->getContent();
    }


}