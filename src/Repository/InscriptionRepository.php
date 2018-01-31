<?php

namespace App\Repository;

use App\Entity\Inscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class InscriptionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Inscription::class);
    }

    public function selectByStatusWaitListed($campus)
    {
        $status = 'waitlisted';
        $query = $this->createQueryBuilder('i')
            ->where('i.status = :waitlisted')
            ->andWhere('i.locality = :campus')
            ->setParameter('waitlisted', $status)
            ->setParameter('campus', $campus)
            ->getQuery();
        return $query->execute();
    }
}
