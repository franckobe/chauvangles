<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupRepository")
 */
class Group
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * Many Groups have Many Users.
     * @ManyToMany(targetEntity="User", mappedBy="groups")
     */
    private $users;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $creator;

    /**
     * @ORM\Column(type="string")
     */
    private $discussionName;


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

    public function setCreator(\int $creatorId): self
    {
        $this->creator = $creatorId;
        return $this;
    }

    public function getCreator(): self
    {
        return $this->creator;
    }

    public function setName(\string $discussName): self
    {
        $this->discussionName = $discussName;
        return $this;
    }

    public function getName(\string $discussName): self
    {
        return $this->$discussName;
    }

    public function __construct() {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }
}
