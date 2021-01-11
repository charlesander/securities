<?php

namespace App\Repository;

use App\Entity\Fact;
use Classes\Expression;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Fact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fact[]    findAll()
 * @method Fact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fact::class);
    }

    /**
     * @param string $security
     * @param string $attribute
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySecurityAndAttribute(string $security, string $attribute)
    {
        $results = $this->createQueryBuilder('f')
            ->join('f.security', 's')
        ->join('f.Attribute', 'a')
            ->andWhere('s.symbol = :security')
            ->andWhere('a.name = :attribute')
            ->setParameter('attribute', $attribute)
            ->setParameter('security', $security)
            ->getQuery()
            ->getOneOrNullResult();
        return (float) $results->getValue();
    }
}
