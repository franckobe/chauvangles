<?php

namespace App\Repository;

use App\Entity\BossNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BossNote|null find($id, $lockMode = null, $lockVersion = null)
 * @method BossNote|null findOneBy(array $criteria, array $orderBy = null)
 * @method BossNote[]    findAll()
 * @method BossNote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BossNoteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BossNote::class);
    }

//    /**
//     * @return BossNote[] Returns an array of BossNote objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BossNote
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
