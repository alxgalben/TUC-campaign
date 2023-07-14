<?php

namespace App\Repository;

use App\Entity\ReceiptRegistration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReceiptRegistration>
 *
 * @method ReceiptRegistration|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReceiptRegistration|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReceiptRegistration[]    findAll()
 * @method ReceiptRegistration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceiptRegistrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReceiptRegistration::class);
    }

    public function weekReceiptsCounter(string $telefon, \DateTimeInterface $date):int {
        $startOfWeek = new \DateTime('first day of this week');
        $endOfWeek = new \DateTime('last day of this week');

        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.telefon = :telefon')
            ->andWhere('r.submittedAt >= :startOfWeek AND r.submittedAt <= :endOfWeek')
            ->setParameter('telefon', $telefon)
            ->setParameter('startOfWeek', $startOfWeek->format('Y-m-d H:i:s'))
            ->setParameter('endOfWeek', $endOfWeek->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function add(ReceiptRegistration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ReceiptRegistration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ReceiptRegistration[] Returns an array of ReceiptRegistration objects
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

//    public function findOneBySomeField($value): ?ReceiptRegistration
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
