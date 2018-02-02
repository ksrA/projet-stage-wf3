<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

//Entité représentant les candidats postulant

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApplicationRepository")
 */
class Application
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $idReunion;

    /**
     * @ORM\Column(type="string")
     */
    private $campus;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string")
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="datetime")
     */
    private $birthday;

    /**
     * @ORM\Column(type="string")
     */
    private $nationality;

    /**
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     */
    private $resume;

    /**
     * @ORM\Column(type="string")
     */
    private $coverLetter;

    /**
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @ORM\Column(type="string")
     */
    private $CityPoleEmploi;

    /**
     * @ORM\Column(type="string")
     */
    private $situation;

    /**
     * @ORM\Column(type="integer", length=4)
     */
    private $yearOfLastDegree;

    /**
     * @ORM\Column(type="string")
     */
    private $lastFormation;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $note;

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
    public function getIdReunion()
    {
        return $this->idReunion;
    }

    /**
     * @param mixed $idReunion
     */
    public function setIdReunion($idReunion): void
    {
        $this->idReunion = $idReunion;
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
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return mixed
     */
    public function getSituation()
    {
        return $this->situation;
    }

    /**
     * @param mixed $situation
     */
    public function setSituation($situation): void
    {
        $this->situation = $situation;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param mixed $phoneNumber
     */
    public function setPhoneNumber($phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return mixed
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param mixed $birthday
     */
    public function setBirthday($birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @return mixed
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * @param mixed $nationality
     */
    public function setNationality($nationality): void
    {
        $this->nationality = $nationality;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getResume()
    {
        return $this->resume;
    }

    /**
     * @param mixed $resume
     */
    public function setResume($resume): void
    {
        $this->resume = $resume;
    }

    /**
     * @return mixed
     */
    public function getCoverLetter()
    {
        return $this->coverLetter;
    }

    /**
     * @param mixed $coverLetter
     */
    public function setCoverLetter($coverLetter): void
    {
        $this->coverLetter = $coverLetter;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getCityPoleEmploi()
    {
        return $this->CityPoleEmploi;
    }

    /**
     * @param mixed $CityPoleEmploi
     */
    public function setCityPoleEmploi($CityPoleEmploi): void
    {
        $this->CityPoleEmploi = $CityPoleEmploi;
    }

    /**
     * @return mixed
     */
    public function getYearOfLastDegree()
    {
        return $this->yearOfLastDegree;
    }

    /**
     * @param mixed $yearOfLastDegree
     */
    public function setYearOfLastDegree($yearOfLastDegree): void
    {
        $this->yearOfLastDegree = $yearOfLastDegree;
    }

    /**
     * @return mixed
     */
    public function getLastFormation()
    {
        return $this->lastFormation;
    }

    /**
     * @param mixed $lastFormation
     */
    public function setLastFormation($lastFormation): void
    {
        $this->lastFormation = $lastFormation;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note): void
    {
        $this->note = $note;
    }



}
