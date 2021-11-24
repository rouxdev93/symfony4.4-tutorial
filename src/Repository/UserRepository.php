<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByAllWithMoreThan5Posts()
    {
        return $this->getFindAllWithMoreThan5PostsQuery()
                    ->getQuery()
                    ->getResult()
        ;
    }

    public function findByAllWithMoreThan5PostsExceptUser(User $user)
    {
            return $this->getFindAllWithMoreThan5PostsQuery()
                        ->andHaving('u != :user')
                        ->setParameter('user', $user)
                        ->getQuery()
                        ->getResult()
            ;
    }

    private function getFindAllWithMoreThan5PostsQuery()
    {
        $qb = $this->createQueryBuilder('u');

        return $qb
            ->innerJoin('u.posts', 'mp')
            ->having('count(mp) >= 5')
            ->groupBy('u')
           ;
    }

}
