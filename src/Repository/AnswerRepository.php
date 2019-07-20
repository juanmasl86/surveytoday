<?php

namespace App\Repository;

use App\Entity\Answer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    // /**
    //  * @return Answer[] Returns an array of Answer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Answer
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    */


    public function findTrue($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.theAnswer LIKE :val AND a.question = :val2')
            ->setParameter('val', 'true')
            ->setParameter('val2', $value)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findFalse($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.theAnswer LIKE :val AND a.question = :val2')
            ->setParameter('val', 'false')
            ->setParameter('val2', $value)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByQuestion($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.question = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }
}
