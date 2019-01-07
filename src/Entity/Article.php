<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $author;

    /**
     * @ORM\Column(type="integer")
     */
    private $nb_views;

    /**
     * @ORM\Column(type="boolean")
     */
    private $published;

    /**
     * @var PersistentCollection
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="article",
     *     cascade={"persist", "remove"})
     */
    private $comments;

    /**
     * @var PersistentCollection
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", mappedBy="articles")
     */
    private $categories;

    /**
     * @var string
     * @ORM\Column(unique=true)
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

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
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

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

    public function getNbViews(): ?int
    {
        return $this->nb_views;
    }

    public function setNbViews(int $nb_views): self
    {
        $this->nb_views = $nb_views;

        return $this;
    }

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return PersistentCollection
     */
    public function getComments(): PersistentCollection
    {
        return $this->comments;
    }

    /**
     * @param PersistentCollection $comments
     */
    public function setComments(PersistentCollection $comments): void
    {
        $this->comments = $comments;
    }

    /**
     * @return PersistentCollection
     */
    public function getCategories(): PersistentCollection
    {
        return $this->categories;
    }

    /**
     * @param PersistentCollection $categories
     */
    public function setCategories(PersistentCollection $categories): void
    {
        $this->categories = $categories;
    }

    /**
     * @Assert\Callback()
     */
    public function isContentValid(ExecutionContextInterface $context)
    {
        if($this->title == $this->content)
        {
            $context
                ->buildViolation("Il est interdit d'utiliser la même valeur pour le nom de la description et le titre")
                ->atPath('content')
                ->addViolation();
        }
    }

    /**
     * @Assert\Callback()
     */
    public function isNbviewsValid(ExecutionContextInterface $context)
    {
        if($this->nb_views < 1)
        {
            $context
                ->buildViolation("Le nombre de vue doit être stricement supérieur")
                ->atPath('nb_views')
                ->addViolation();
        }
    }

    /**
     * @ORM\PrePersist()
     */
    public function authorArticle()
    {
        if(is_null($this->author))
        {
            $this->author = "anonymous";
        }
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }



}
