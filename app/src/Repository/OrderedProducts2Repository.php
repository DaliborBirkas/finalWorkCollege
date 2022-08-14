<?php

namespace App\Repository;

use App\Entity\OrderedProducts2;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderedProducts2>
 *
 * @method OrderedProducts2|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderedProducts2|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderedProducts2[]    findAll()
 * @method OrderedProducts2[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderedProducts2Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderedProducts2::class);
    }

    public function add(OrderedProducts2 $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OrderedProducts2 $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return OrderedProducts2[] Returns an array of OrderedProducts2 objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?OrderedProducts2
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
