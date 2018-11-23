<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PrivateMessageRepository")
 * @ORM\Table(name="`PrivateMessage`")
 */
class PrivateMessage
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_emission;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_reception;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_read;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="id_receiver", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $sender;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $receiver;

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

    public function getDateEmission(): ?\DateTimeInterface
    {
        return $this->date_emission;
    }

    public function setDateEmission(\DateTimeInterface $date_emission): self
    {
        $this->date_emission = $date_emission;

        return $this;
    }

    public function getDateReception(): ?\DateTimeInterface
    {
        return $this->date_reception;
    }

    public function setDateReception(\DateTimeInterface $date_reception): self
    {
        $this->date_reception = $date_reception;

        return $this;
    }

    public function getDateRead(): ?\DateTimeInterface
    {
        return $this->date_read;
    }

    public function setDateRead(\DateTimeInterface $date_read): self
    {
        $this->date_read = $date_read;

        return $this;
    }

    public function getIdSender(): ?User
    {
        return $this->sender;
    }

    public function setIdSender(User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getIdReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setIdReceiver(User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }
}
