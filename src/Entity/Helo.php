<?php

namespace App\Entity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use App\Repository\HeloRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\Timestampable;


/**
 * @ORM\Entity(repositoryClass=HeloRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Helo
{
    use TimestampableEntity;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $age;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(?string $age): self
    {
        $this->age = $age;

        return $this;
    }





}
