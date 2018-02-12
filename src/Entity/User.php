<?php

    namespace App\Entity;

    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Security\Core\User\UserInterface;

    //Entité représentant les users du back office

    /**
    * @ORM\Table(name="app_users")
    * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
    */
    class User implements UserInterface, \Serializable
    {
    /**
    * @ORM\Column(type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @ORM\Column(type="string", length=30, unique=true)
    */
    private $username;

    /**
    * @ORM\Column(type="string", length=255)
    */
    private $password;

    /**
    * @ORM\Column(type="string", length=255, unique=true)
    */
    private $email;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
    * @ORM\Column(name="is_active", type="boolean")
    */
    private $isActive;

    /**
     * @ORM\Column(name="reset_hash", type="string")
     */
    private $resethash;

    public function __construct()
    {
        $this->isActive = true;
        $this->roles = new ArrayCollection();
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid('', true));
    }

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
    public function getisActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive): void
    {
        $this->isActive = $isActive;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }
    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function eraseCredentials()
    {
    }

    public function setResetHash($resethash)
    {
        $this->resethash = $resethash;
    }

    public function getResetHash()
    {
        return $this->resethash;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
        $this->id,
        $this->username,
        $this->password,
        // see section on salt below
        // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
        $this->id,
        $this->username,
        $this->password,
        // see section on salt below
        // $this->salt
        ) = unserialize($serialized);
    }
}