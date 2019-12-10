<?php

namespace App\Repository;

use App\Entity\Advert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Advert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advert[]    findAll()
 * @method Advert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advert::class);
    }
    public function findAllByIdCreator($id) {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :id')
            ->setParameter('id', $id)
            ->orderBy('a.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findByName($name, $id) {
        return $this->createQueryBuilder('a')
            ->andWhere('a.name = :name AND a.user = :id')
            ->setParameter('name', $name)
            ->setParameter('id', $id)
            ->orderBy('a.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findAllByName($name) {
        return $this->createQueryBuilder('a')
            ->andWhere('a.name LIKE :name')
            ->setParameter('name', '%'.$name.'%')
            ->orderBy('a.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return Advert[] Returns an array of Advert objects
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
    public function findOneBySomeField($value): ?Advert
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}