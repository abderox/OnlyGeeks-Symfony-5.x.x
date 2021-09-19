<?php

namespace App\Entity;
use App\Service\Uploader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use App\Repository\HeloRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * @ORM\Entity(repositoryClass=HeloRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity
 * @Vich\Uploadable
 * @UniqueEntity("age",message="That's a 'lucky number' of another post , Sorry")
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
     *      min = 4,
     *      max = 25,
     *      minMessage = "The title must be at least {{ limit }} characters long",
     *      maxMessage = "The title cannot be longer than {{ limit }} characters"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="bigint", nullable=true, unique=true)
     * @Assert\Regex(
     *     pattern     = "/^[0-9]+$/i",
     *     htmlPattern = "[0-9]+"
     * )
     *
     */
    private $age;


    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="pin_image", fileNameProperty="image")
     * @Assert\Image(maxSize="8M")
     * @var File|null
     */
    private $imageFile;
    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="helos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $service;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $noma;

    /**
     * @ORM\OneToMany(targetEntity=PostLike::class, mappedBy="pin")
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity=PostComment::class, mappedBy="pin")
     * @ORM\OrderBy({"createdAt"="DESC"})
     */
    private $postComments;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $content;

    /**
     * @ORM\OneToMany(targetEntity=PostDislikes::class, mappedBy="pin")
     */
    private $postDislikes;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="pins",cascade={"persist"})
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity=Reference::class, mappedBy="pin")
     */
    private $reference;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isPublished;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;



    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->postComments = new ArrayCollection();
        $this->postDislikes = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->reference = new ArrayCollection();

    }


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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getService(): ?string
    {

        return $this->service;
    }

    public function setService(?string $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getNoma(): ?string
    {
        return $this->noma;
    }

    public function setNoma(?string $noma): self
    {
        $this->noma = $noma;

        return $this;
    }

    /**
     * @return Collection|PostLike[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(PostLike $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setPin($this);
        }

        return $this;
    }

    public function removeLike(PostLike $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getPin() === $this) {
                $like->setPin(null);
            }
        }

        return $this;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isLikedBy(User $user): bool
    {
        foreach ($this->likes as $like) {
            if ($like->getUser() === $user) return true ;
            }
        return false;
    }
    /**
     * @param User $user
     * @return bool
     */
    public function isDisLikedBy(User $user): bool
    {
        foreach ($this->postDislikes as $like) {
            if ($like->getUser() === $user) return true ;
        }
        return false;
    }
    /**
     * @param User $user
     * @return bool
     */
    public function isCommentedBy(User $user): bool
    {
        foreach ($this->postComments as $like) {
            if ($like->getUser() === $user) return true ;
        }
        return false;
    }


    /**
     * @return Collection|PostComment[]
     */
    public function getPostComments(): Collection
    {
        return $this->postComments;
    }
    /* use criteria for looping instead of using the traditionnal way ! for loops*/
    /**
     * @return Collection|PostComment[]
     */
    public function getNoNdeletedPostComments(): Collection
    {
        $criteria = HeloRepository::NondeletedPostComments();
        return $this->postComments->matching($criteria);
    }
    /**
     * @return Collection|PostComment[]
     */
    public function getdeletedPostComments(): Collection
    {
        $criteria = HeloRepository::CountDeletedCommnets();
        return $this->postComments->matching($criteria);
    }

    public function addPostComment(PostComment $postComment): self
    {
        if (!$this->postComments->contains($postComment)) {
            $this->postComments[] = $postComment;
            $postComment->setPin($this);
        }

        return $this;
    }

    public function removePostComment(PostComment $postComment): self
    {
        if ($this->postComments->removeElement($postComment)) {
            // set the owning side to null (unless already changed)
            if ($postComment->getPin() === $this) {
                $postComment->setPin(null);
            }
        }

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection|PostDislikes[]
     */
    public function getPostDislikes(): Collection
    {
        return $this->postDislikes;
    }

    public function addPostDislike(PostDislikes $postDislike): self
    {
        if (!$this->postDislikes->contains($postDislike)) {
            $this->postDislikes[] = $postDislike;
            $postDislike->setPin($this);
        }

        return $this;
    }

    public function removePostDislike(PostDislikes $postDislike): self
    {
        if ($this->postDislikes->removeElement($postDislike)) {
            // set the owning side to null (unless already changed)
            if ($postDislike->getPin() === $this) {
                $postDislike->setPin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * @return Collection|Reference[]
     */
    public function getReference(): Collection
    {
        return $this->reference;
    }

    public function addReference(Reference $reference): self
    {
        if (!$this->reference->contains($reference)) {
            $this->reference[] = $reference;
            $reference->setPin($this);
        }

        return $this;
    }

    public function removeReference(Reference $reference): self
    {
        if ($this->reference->removeElement($reference)) {
            // set the owning side to null (unless already changed)
            if ($reference->getPin() === $this) {
                $reference->setPin(null);
            }
        }

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(?bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }









}
