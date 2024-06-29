<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use App\Repository\RestaurantRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("api/menu", name: "api_app_menu_")]
class MenuController extends AbstractController {

    public function __construct(private EntityManagerInterface $manager, private RestaurantRepository $restaurantRepository, 
                    private MenuRepository $menuRepository)
    {
        
    }

    #[Route("/", name: "create", methods: ["POST"])]
    public function create(): Response
    {
        $restaurant = $this->restaurantRepository->findOneBy(["id" => 2]);
        $menu = new Menu();
        $menu->setRestaurant($restaurant);
        $menu->setUuid(uniqid());
        $menu->setTitle("Menu de test de l'api du restaurant");
        $menu->setDescription("Venez découvrir des mets authentique pleins de lignes de codes...");
        $menu->setPrice(35);
        $menu->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($menu);
        $this->manager->flush();

        return $this->json([
            "message" => "Menu was created with uuid : {$menu->getUuid()}"
        ]);
    }

    #[Route("/{id}", name: "show", methods: ["GET"], requirements: ["id" => "\d+"])]
    public function show(int $id): Response
    {
        $menu = $this->menuRepository->findOneBy(["id" => $id]);

        return $this->json([
            "message" => "Menu was found with uuid : {$menu->getUuid()}"
        ]);
    }

    #[Route("/{id}", name: "edit", methods: ["PUT"], requirements: ["id" => "\d+"])]
    public function edit(int $id): Response
    {
        $menu = $this->menuRepository->findOneBy(["id" => $id]);
        $menu->setTitle("Title menu modified");
        $menu->setUpdatedAt(new DateTime());

        return $this->json([
            "message" => "Menu was modifier with uuid : {$menu->getUuid()}, new name : {$menu->getTitle()} at {$menu->getUpdatedAt()->format('Y-m-d H:i:s')}"
        ]);
    }

    #[Route("/{id}", name: "delete", methods: ["DELETE"], requirements: ["id" => "\d+"])]
    public function delete(int $id): Response
    {
        $menu = $this->menuRepository->findOneBy(["id" => $id]);
        
        $this->manager->remove($menu);
        $this->manager->flush();

        return $this->json([
            "message" => "Menu with uuid {$menu->getUuid()} was deleted."
        ]);
    }
}
