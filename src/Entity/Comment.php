<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ("quack")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ("quack")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups ("quack")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Quack::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $quack;

    /**
     * @ORM\ManyToOne(targetEntity=Duck::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups ("quack")
     */
    private $author;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getQuack(): ?Quack
    {
        return $this->quack;
    }

    public function setQuack(?Quack $quack): self
    {
        $this->quack = $quack;

        return $this;
    }

    public function getAuthor(): ?Duck
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }
}
