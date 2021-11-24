<?php

namespace App\Repository;

use App\Entity\MicroPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MicroPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method MicroPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method MicroPost[]    findAll()
 * @method MicroPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MicroPostRepository extends ServiceEntityRepository
{
    /**
     * MicroPostRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MicroPost::class);
    }

    /**
     * @param Collection $users
     */
    public function findAllPostByFollowing(Collection $users)
    {
        $qb = $this->createQueryBuilder('mp');

        return $qb
                ->where('mp.user in (:following)')
                ->setParameter('following', $users)
                ->orderBy('mp.datetime', 'ASC')
                //->setMaxResults(10)
                ->getQuery()
                ->getResult()
        ;
    }
}
