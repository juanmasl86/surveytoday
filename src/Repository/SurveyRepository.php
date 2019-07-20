<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Survey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Survey|null find($id, $lockMode = null, $lockVersion = null)
 * @method Survey|null findOneBy(array $criteria, array $orderBy = null)
 * @method Survey[]    findAll()
 * @method Survey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SurveyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Survey::class);
    }

    // /**
    //  * @return Survey[] Returns an array of Survey objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Survey
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findOneById($value): ?Survey
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

   /* public function findNotIn($value): ?Survey
    {*/
    /* Seleciona el id de encuestas que no este en la subconsulta con la id del usuario. */
    /* SELECT id FROM survey WHERE id NOT IN (SELECT survey_id FROM survey_user WHERE user_id = $value.id) */
        
   /* $conn = $this->getEntityManager()->getConnection();

    $subquery = 'SELECT survey_id FROM survey_user WHERE user_id = ?';
    $stmt = $conn->prepare($subquery);
    $stmt->bindValue(1, $value);
    $stmt->execute();
    $result = $stmt->fetchAll();
    var_dump($result); die;
    return $this->createQueryBuilder('s')
            ->Where('s.id NOT IN :val')
            ->setParameter('val', $result)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }*/
}
