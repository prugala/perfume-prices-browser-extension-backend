<?php

namespace App\Repository;

use App\Entity\ProductLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductLink>
 *
 * @method ProductLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductLink[]    findAll()
 * @method ProductLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductLinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductLink::class);
    }

    public function save(ProductLink $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProductLink $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
