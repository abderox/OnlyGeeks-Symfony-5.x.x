<?php

namespace App\Entity;

use App\Repository\PostDislikesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostDislikesRepository::class)
 */
class PostDislikes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="postDislikes")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Helo::class, inversedBy="postDislikes")
     */
    private $pin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPin(): ?Helo
    {
        return $this->pin;
    }

    public function setPin(?Helo $pin): self
    {
        $this->pin = $pin;

        return $this;
    }
}
