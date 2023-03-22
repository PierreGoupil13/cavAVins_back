<?php

namespace App\Repository;

use App\Entity\ResetToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResetToken>
 *
 * @method ResetToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResetToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResetToken[]    findAll()
 * @method ResetToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResetTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResetToken::class);
    }

    public function save(ResetToken $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ResetToken $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findResetToken(int $bearer_id): mixed
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.bearer = :val')
            ->setParameter('val', $bearer_id)
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return ResetToken[] Returns an array of ResetToken objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ResetToken
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
