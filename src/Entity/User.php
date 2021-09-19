<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface ,\Serializable
{
    use TimestampableEntity;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255 ,nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255 ,nullable=true)
     */
    private $prename;
    /**
     * @var string|null
     */
    private $fullname;


    /**
     * @ORM\Column(type="boolean")
     */
    private $is_verified=false;

    /**
     * @ORM\OneToMany(targetEntity=Helo::class, mappedBy="user", orphanRemoval=true)
     */
    private $helos;

    /**
     * @ORM\OneToMany(targetEntity=PostLike::class, mappedBy="user")
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity=PostComment::class, mappedBy="user")
     */
    private $postcomments;

    /**
     * @ORM\OneToMany(targetEntity=PostDislikes::class, mappedBy="user")
     */
    private $postDislikes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $OGusername;

    /**
     * @ORM\Column(type="smallint", nullable=true ,unique=true)
     */
    private $gender;

    /**
     * @ORM\OneToMany(targetEntity=FriendShip::class, mappedBy="user")
     */
    private $friends;

    /**
     * @ORM\OneToMany(targetEntity=FriendShip::class, mappedBy="friend")
     */
    private $myfriends;

    /**
     * @ORM\OneToOne(targetEntity=Profile::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $profile;



    public function __construct()
    {
        $this->helos = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->postcomments = new ArrayCollection();
        $this->postDislikes = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->myfriends = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrename(): ?string
    {
        return $this->prename;
    }

    public function setPrename(string $prename): self
    {
        $this->prename = $prename;

        return $this;
    }




    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    public function setIsVerified(bool $isVerified): self
    {

        $this->is_verified = $isVerified;

        return $this;

    }

    public function getIsVerified(): ?bool
    {
        return $this->is_verified;
    }

    /**
     * @return Collection|Helo[]
     */
    public function getHelos(): Collection
    {
        return $this->helos;
    }

    public function addHelo(Helo $helo): self
    {
        if (!$this->helos->contains($helo)) {
            $this->helos[] = $helo;
            $helo->setUser($this);
        }

        return $this;
    }

    public function removeHelo(Helo $helo): self
    {
        if ($this->helos->removeElement($helo)) {
            // set the owning side to null (unless already changed)
            if ($helo->getUser() === $this) {
                $helo->setUser(null);
            }
        }

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
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(PostLike $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
            }
        }

        return $this;
    }
    public function getFullName(): string
    {
        $fullname=$this->getName()." ".$this->getPrename();
        return  $fullname;
    }

    /**
     * @return Collection|PostComment[]
     */
    public function getPostcomment(): Collection
    {
        return $this->postcomments;
    }

    public function addPostcoment(PostComment $postcomments): self
    {
        if (!$this->postcomments->contains($postcomments)) {
            $this->postcomments[] = $postcomments;
            $postcomments->setUser($this);
        }

        return $this;
    }

    public function removePostcomment(PostComment $postcomments): self
    {
        if ($this->postcomments->removeElement($postcomments)) {
            // set the owning side to null (unless already changed)
            if ($postcomments->getUser() === $this) {
                $postcomments->setUser(null);
            }
        }

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
            $postDislike->setUser($this);
        }

        return $this;
    }

    public function removePostDislike(PostDislikes $postDislike): self
    {
        if ($this->postDislikes->removeElement($postDislike)) {
            // set the owning side to null (unless already changed)
            if ($postDislike->getUser() === $this) {
                $postDislike->setUser(null);
            }
        }

        return $this;
    }

    public function getOGusername(): ?string
    {
        return $this->OGusername;
    }

    public function setOGusername(?string $OGusername): self
    {
        $this->OGusername = $OGusername;

        return $this;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setGender(?int $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return Collection|FriendShip[]
     */
    public function getFriends(): Collection
    {
        return $this->friends;
    }

    public function addFriend(FriendShip $friend): self
    {
        if (!$this->friends->contains($friend)) {
            $this->friends[] = $friend;
            $friend->setUser($this);
        }

        return $this;
    }

    public function removeFriend(FriendShip $friend): self
    {
        if ($this->friends->removeElement($friend)) {
            // set the owning side to null (unless already changed)
            if ($friend->getUser() === $this) {
                $friend->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FriendShip[]
     */
    public function getMyfriends(): Collection
    {
        return $this->myfriends;
    }

    public function addMyfriend(FriendShip $myfriend): self
    {
        if (!$this->myfriends->contains($myfriend)) {
            $this->myfriends[] = $myfriend;
            $myfriend->setFriend($this);
        }

        return $this;
    }

    public function removeMyfriend(FriendShip $myfriend): self
    {
        if ($this->myfriends->removeElement($myfriend)) {
            // set the owning side to null (unless already changed)
            if ($myfriend->getFriend() === $this) {
                $myfriend->setFriend(null);
            }
        }

        return $this;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function AmIfollowing(User $user): bool
    {
        foreach ($this->getMyfriends() as $friend) {
            if($friend->getUser()->getId()===$user->getId()) return true;
        }
        return false;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        // unset the owning side of the relation if necessary
        if ($profile === null && $this->profile !== null) {
            $this->profile->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($profile !== null && $profile->getUser() !== $this) {
            $profile->setUser($this);
        }

        $this->profile = $profile;

        return $this;
    }


    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            ) = unserialize($serialized);

    }
}
