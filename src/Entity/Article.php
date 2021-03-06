<?php

declare(strict_types=1);

namespace App\Entity;

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

    // public function __construct(Security $security)
    // {
    //     $this->security = $security;
    // }

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
    // public function setCreatedAt(\DateTime $createdAt): self
    // {
    //     $this->createdAt = $createdAt;
    //
    //     return $this;
    // }

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

    // /**
    //  * @ORM\PrePersist
    //  */
    // public function preSetUser()
    // {
    //     if (null === $this->user) {
    //         $this->user = $this->security->getUser();
    //     }
    // }

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
}
