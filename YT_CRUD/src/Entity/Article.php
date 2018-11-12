<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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
  * @ORM\Column(type="text", length=100)
  */
  private $title;

  /**
  * @ORM\Column(type="text", length=100)
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
}
