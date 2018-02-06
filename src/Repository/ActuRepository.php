<?php

namespace App\Repository;

use App\Entity\Actu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ActuRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Actu::class);
    }


    public function findAllByOrderDesc()
    {
        $query = $this->createQueryBuilder('a')
            ->orderBy('a.date', 'DESC')
            ->getQuery();
        return $query->execute();
    }

    //Requetes pour récupérer les dernieres actus
    //Du plus récent au plus vieux (desc par date)
    //Pour le footer (3) et pour la aside (5)
    public function findTheLastActu($nb = null)
    {
        if ($nb != null){
            $query = $this->createQueryBuilder('a')
                ->orderBy('a.date', 'DESC')
                ->setMaxResults($nb)
                ->getQuery();
            return $query->execute();
        }
        else{
            $query = $this->createQueryBuilder('a')
                ->orderBy('a.date', 'DESC')
                ->setMaxResults(3)
                ->getQuery();
            return $query->execute();
        }
    }

    //Récupère une liste d'articles triés et paginés.

    public function findAllPaginedAndSorted($page, $nbActuPerPage, $count = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->orderBy('a.date', 'DESC');

        $query = $qb->getQuery();

        //Calcul pour avoir le bon premier resultat de requete

        $firstResult = ($page - 1) * $nbActuPerPage;
        $query->setFirstResult($firstResult)->setMaxResults($nbActuPerPage);
        $result = $query->execute();

        //Instanciation d'un objet de type paginator avec la requete

        $paginator = new Paginator($query);

        if ($paginator->count() <= $firstResult) {
            return false;
        }

        if ($count != null){
            return $paginator;
        }
        else {
            return $result;
        }
    }

}
