<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as MyAssert;

/**
* @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
*/
class Article
{
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
  * @ORM\Column()
  * @Assert\NotBlank(
  *     message = "To pole nie może być puste"
  * )
  * @MyAssert\IsExistingUser
  */
  private $author;

  /**
  * @ORM\Column(type="datetime")
  */
  private $createdAt;

  /**
  * @ORM\Column(type="boolean")
  */
  private $isPublished;

  /**
  * @ORM\Column(type="text")
  */
  private $body;

  /**
   * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="articles")
   * @ORM\JoinColumn(nullable=false)
   */
  private $user;

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

  public function getAuthor(): ?string
  {
    return $this->author;
  }

  public function setAuthor(string $author): self
  {
    $this->author = $author;

    return $this;
  }

  public function getCreatedAt(): ?\DateTime
  {
    return $this->createdAt;
  }

  public function setCreatedAt(\DateTime $createdAt): self
  {
    $this->createdAt = $createdAt;

    return $this;
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
}
