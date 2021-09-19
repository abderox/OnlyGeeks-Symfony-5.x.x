<?php

namespace App\Entity;

use App\Repository\ReferenceRepository;
use App\Service\Uploader;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ReferenceRepository::class)
 */
class Reference
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("main")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $source;

    /**
     * @ORM\ManyToOne(targetEntity=Helo::class, inversedBy="reference")
     */
    private $pin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("main")
     */
    private $filename;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("main")
     */
    private $originalFilename;

    /**
     * @Groups("main")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mimetype;

    public function __construct(Helo $helo)
    {
    $this->pin=$helo;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSource(): ?bool
    {
        return $this->source;
    }

    public function setSource(?bool $source): self
    {
        $this->source = $source;

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

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(?string $originalFilename): self
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    public function getMimetype(): ?string
    {
        return $this->mimetype;
    }

    public function setMimetype(?string $mimetype): self
    {
        $this->mimetype = $mimetype;

        return $this;
    }
    public function getFilePath(): string
    {
        return Uploader::ARTICLE_REFERENCE .'/'.$this->getFilename();
    }
}
