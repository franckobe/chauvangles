<?php

namespace App\Entity;

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
    private $id;

    /**
     * @ORM\Column(name="creator",type="integer")
     */
    private $creator;

    /**
     * @ORM\Column(name="discussionName",type="string")
     */
    private $discussionName;

    /**
     * @ORM\Column(name="date_creation",type="datetime")
     */
    private $date_creation;

    /**
     * Many Groups have Many Users.
     * @ManyToMany(targetEntity="User", mappedBy="groups", cascade={"persist"})
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

    public function getCreator(): self
    {
        return $this->creator;
    }

    public function setName( $discussName): self
    {
        $this->discussionName = $discussName;
        return $this;
    }

    public function getName(): self
    {
        return $this->discussionName;
    }

    public function __construct() {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function addUser(User $users)
    {
        $this->users[] = $users;
        return $this;
    }

}
