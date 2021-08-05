<?php

namespace App\Entity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use App\Repository\HeloRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * @ORM\Entity(repositoryClass=HeloRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity
 * @Vich\Uploadable
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
     * @Assert\NotBlank(message="can't be blank")
     * @Assert\Length(
     *      min = 10,
     *      max = 50,
     *      minMessage = "The title must be at least {{ limit }} characters long",
     *      maxMessage = "The title cannot be longer than {{ limit }} characters"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     * @Assert\Regex(
     *     pattern     = "/^[0-9]+$/i",
     *     htmlPattern = "[0-9]+"
     * )
     */
    private $age;



    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="pin_image", fileNameProperty="image")
     * @Assert\Image(maxSize="8M")
     * @Assert\NotBlank
     * @var File|null
     */
    private $imageFile;
    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $image;



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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }





}
