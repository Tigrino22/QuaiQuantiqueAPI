<?php

namespace App\Controller;

use App\Entity\Food;
use App\Repository\FoodRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/food', name: 'api_app_food_')]
class FoodController extends AbstractController
{

    public function __construct(private EntityManagerInterface $manager, private FoodRepository $foodRepository)
    {
        
    }
    #[Route("/{id}", name: "show", requirements: ["id" => "\d+"], methods: ["GET"])]
    public function show(Request $request, int $id): JsonResponse
    {

        $food = $this->foodRepository->findOneBy(["id" => $id]);


        return $this->json([
            "message" => "Food was found with uuid {$food->getUuid()}"
        ]);
    }

    #[Route("/", name: "create", methods: "POST")]
    public function create(Request $request): JsonResponse
    {

        $food = new Food();
        $food->setUuid(uniqid());
        $food->setTitle("Food numero 1");
        $food->setDescription("La première nourriture créée sur l'API");
        $food->setPrice(10);
        $food->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($food);
        $this->manager->flush();

        return $this->json([
            "message" => "Food was created with uuid : {$food->getUuid()}"
        ]);
    }

    #[Route("/{id}", name: "edit", requirements: ["id" => "\d+"], methods: "PUT")]
    public function edit(Request $request, int $id): JsonResponse
    {

        $food = $this->foodRepository->findOneBy(["id" => $id]);
        
        $food->setTitle("Food numero 1 with updated title");
        $food->setPrice(15);
        $food->setUpdatedAt(new DateTimeImmutable());

        $this->manager->flush();

        return $this->json([
            "message" => "Food was updated with uuid : {$food->getUuid()}"
        ]);
    }

    #[Route("/{id}", name: "delete", requirements: ["id" => "\d+"], methods: "DELETE")]
    public function delete(Request $request, int $id): JsonResponse
    {

        $food = $this->foodRepository->findOneBy(["id" => $id]);
        
        

        $this->manager->remove($food);
        $this->manager->flush();

        return $this->json([
            "message" => "Food with uuid : {$food->getUuid()} was deleted succefully"
        ]);
    }
}
