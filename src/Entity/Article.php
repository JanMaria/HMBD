<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Security;

/**
* @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
* @ORM\HasLifecycleCallbacks
*/
class Article
{
    private $security;

    /**
    * @ORM\Id()
    * @ORM\GeneratedValue()
    * @ORM\Column(type="integer")
    */
    private $id;

    /**
    * @ORM\Column()
    * @Assert\NotBlank(
    *     message = "To pole nie może być puste"
    * )
    */
    private $title;

    /**
    * @ORM\Column(type="datetime")
    */
    private $createdAt;

    /**
    * @ORM\Column(type="boolean")
    */
    private $isPublished = false;

    /**
    * @ORM\Column(type="text")
    */
    private $body;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="articles")
     * @ORM\JoinColumn()
     */
    private $user;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\File(
     *      maxSize = "1M",
     *      mimeTypes = "image/*",
     *      maxSizeMessage = "Plik jest zbyt duży. Maksymalny dopuszczalny rozmiar to {{ limit }} {{ sufix }}.",
     *      mimeTypesMessage = "Nieprawidłowy format pliku. Dopuszczalne wyłącznie pliki graficzne."
     * )
     */
    private $image;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="article", orphanRemoval=true)
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function setTimestamp()
    {
        $this->createdAt = new \DateTime('now');
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
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

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }
}
