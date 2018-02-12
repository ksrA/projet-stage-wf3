<?php

namespace App\Repository;

use App\Entity\SessionFormation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class SessionFormationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SessionFormation::class);
    }


    public function findAllOrderDesc()
    {
        $query =  $this->createQueryBuilder('s')
            ->orderBy('s.id', 'DESC')
            ->getQuery();
        return $query->execute();
    }
}
