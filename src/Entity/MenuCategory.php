<?php

namespace App\Entity;

use App\Repository\MenuCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuCategoryRepository::class)]
class MenuCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'menuCategories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Menu $menuID = null;

    #[ORM\ManyToOne(inversedBy: 'menuCategories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $categoryID = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMenuID(): ?Menu
    {
        return $this->menuID;
    }

    public function setMenuID(?Menu $menuID): static
    {
        $this->menuID = $menuID;

        return $this;
    }

    public function getCategoryID(): ?Category
    {
        return $this->categoryID;
    }

    public function setCategoryID(?Category $categoryID): static
    {
        $this->categoryID = $categoryID;

        return $this;
    }
}
