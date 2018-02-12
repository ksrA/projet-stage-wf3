<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

// Entité représentant la session de formation en bdd

/**
 * @ORM\Entity(repositoryClass="App\Repository\SessionFormationRepository")
 */
class SessionFormation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $campus;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateReunion;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateSession;

    /**
     * @ORM\Column(type="string")
     */
    private $financer;

    /**
     * @ORM\Column(type="string")
     */
    private $hashLink;


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @param mixed $campus
     */
    public function setCampus($campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return mixed
     */
    public function getDateReunion()
    {
        return $this->dateReunion;
    }

    /**
     * @param mixed $dateReunion
     */
    public function setDateReunion($dateReunion): void
    {
        $this->dateReunion = $dateReunion;
    }

    /**
     * @return mixed
     */
    public function getDateSession()
    {
        return $this->dateSession;
    }

    /**
     * @param mixed $dateSession
     */
    public function setDateSession($dateSession): void
    {
        $this->dateSession = $dateSession;
    }

    /**
     * @return mixed
     */
    public function getFinancer()
    {
        return $this->financer;
    }

    /**
     * @param mixed $financer
     */
    public function setFinancer($financer): void
    {
        $this->financer = $financer;
    }

    /**
     * @return mixed
     */
    public function getHashLink()
    {
        return $this->hashLink;
    }

    /**
     * @param mixed $hashLink
     */
    public function setHashLink($hashLink): void
    {
        $this->hashLink = $hashLink;
    }


}
