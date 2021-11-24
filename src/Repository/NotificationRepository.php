<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    /**
     * NotificationRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * @param User $user
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findUnSeenByUser(User $user)
    {
        $qb = $this->createQueryBuilder('n');

        return  $qb ->select('count(n)')
            ->where('n.user = :user')
            ->andWhere('n.seen = 0')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    /**
     * @param User $user
     * @return int|mixed|string
     */
    public function markAllAsReadByUser(User $user)
    {
        $qb = $this->createQueryBuilder('n');

        return $qb
            ->update(Notification::class, 'n')
            ->set('n.seen', true)
            ->where('n.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute()
        ;
    }
}
