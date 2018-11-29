<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupRepository")
 * @ORM\Table(name="`Group`")
 * @UniqueEntity(fields="discussionName", message="Discussion Name is already taken.")
 */
class Group
{
    /**
     * @ORM\Id
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\Column(name="creator",type="integer")
     */
    private $creator;

    /**
     * @ORM\Column(name="discussionName",type="string")
     */
    public $discussionName;

    /**
     * @ORM\Column(name="date_creation",type="datetime")
     */
    private $date_creation;

    /**
     * Many Users have Many Groups.
     * @ManyToMany(targetEntity="User", mappedBy="groups")
     */
    private $users;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;
        return $this;
    }

    public function setCreator( $creatorId): self
    {
        $this->creator = $creatorId;
        return $this;
    }

    public function getCreator()
    {
        return $this->creator;
    }

    public function setName( $discussName): self
    {
        $this->discussionName = $discussName;
        return $this;
    }

    public function getName(): string
    {
        return $this->discussionName;
    }

    public function getUser($user_id)
    {
        //IF USER IS NOT IN THE GROUP RETURN FALSE
        return true;

        //IF USER IS IN THE GROUP RETURN TRUE
//        return false;
    }


    public function __construct() {
        $this->users = new ArrayCollection();
    }

    public function addUser(User $users)
    {
        $this->users[] = $users;
        return $this;
    }

    public function getUsers() : Collection
    {
        return $this->users;
    }

    public function removeUsers(User $user)
    {
        return $this->users->removeElement($user);
    }

}
