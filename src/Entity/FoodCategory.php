<?php

namespace App\Entity;

use App\Repository\FoodCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FoodCategoryRepository::class)]
class FoodCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'foodCategories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Food $foodID = null;

    #[ORM\ManyToOne(inversedBy: 'foodCategories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $categoryID = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFoodID(): ?Food
    {
        return $this->foodID;
    }

    public function setFoodID(?Food $foodID): static
    {
        $this->foodID = $foodID;

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
