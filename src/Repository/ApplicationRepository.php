<?php

namespace App\Repository;

use App\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Application::class);
    }


    public function checkEmailAndIdReunion($email, $id)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.idReunion = :id')
            ->andWhere('a.email = :email')
            ->setParameter('id', $id)
            ->setParameter('email', $email)
            ->setMaxResults(10)
            ->getQuery();
        return $query->execute();
    }

    public function findByIdAndNote($id, $noteRef)
    {
        $query = $this->createQueryBuilder('a')
            ->where('a.idReunion = :id')
            ->andWhere('a.note >= :note')
            ->setParameter('id', $id)
            ->setParameter('note', $noteRef)
            ->getQuery();
        return $query->execute();
    }

}
