<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\JoinTable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupMessageRepository")
 * @ORM\Table(name="`GroupMessage`")
 */
class GroupMessage
{
    /**
     * @ORM\Id
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     * @Groups("group3")
     */
    private $id;

    /**
     * @ORM\Column(name="content",type="text")
     * @Groups("group4")
     */
    private $content;

    /**
     * @ORM\Column(name="date_emission",type="datetime")
     */
    private $date_emission;

    /**
     * @ORM\Column(name="date_reception",type="datetime")
     */
    private $date_reception;

    /**
     * @ORM\Column(name="date_read",type="datetime")
     */
    private $date_read;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Group", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $group_;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDateEmission()
    {
        return $this->date_emission;
    }

    public function setDateEmission($date_emission): self
    {
        $this->date_emission = $date_emission;

        return $this;
    }

    public function getDateReception()
    {
        return $this->date_reception;
    }

    public function setDateReception($date_reception): self
    {
        $this->date_reception = $date_reception;

        return $this;
    }

    public function getDateRead(): ?\DateTimeInterface
    {
        return $this->date_read;
    }

    public function setDateRead($date_read): self
    {
        $this->date_read = $date_read;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getGroup(): ?Group
    {
        return $this->group_;
    }

    public function setGroup(Group $group_): self
    {
        $this->group_ = $group_;

        return $this;
    }
}
