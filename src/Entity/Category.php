<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
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
    private $name;

    /**
     * @var PersistentCollection
     * @ORM\ManyToMany(targetEntity="App\Entity\Article", inversedBy="categories")
     */
    private $articles;

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return PersistentCollection
     */
    public function getArticles(): PersistentCollection
    {
        return $this->articles;
    }

    /**
     * @param PersistentCollection $articles
     */
    public function setArticles(PersistentCollection $articles): void
    {
        $this->articles = $articles;
    }

}
