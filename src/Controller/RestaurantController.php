<?php

namespace App\Controller;

use DateTimeImmutable;
use DateTime;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("api/restaurant", name: "api_app_restaurant_")]
class RestaurantController extends AbstractController {

    public function __construct(private EntityManagerInterface $manager, private RestaurantRepository $repository)
    {
    }

    #[Route("/{id}", name: "show", methods: "GET")]
    public function show(int $id): Response
    {

        $restaurant = $this->repository->findOneBy(['id' => $id]);

        if (!$restaurant) {
            throw $this->createNotFoundException("No restaurant found for id {$id}");
        }

        return $this->json(
            [
                'message' => "A restaurant was found : {$restaurant->getName()} with id {$restaurant->getId()}.",
            ]
            );
    }

    #[Route("/", name: "create", methods: "POST")]
    public function create(): Response
    {

          
        $restaurant = new Restaurant();
        $restaurant->setUuid(uniqid());
        $restaurant->setName('Quai Quantique');
        $restaurant->setDescription('Cette qualité et ce goût chez le chef Arnauld Michant');
        $restaurant->setAmOpeningTime([]);
        $restaurant->setPmOpeningTime([]);
        $restaurant->setMaxGuest(100);
        $restaurant->setCreatedAt(new DateTimeImmutable());

        
        // A stocker en base
        $this->manager->persist($restaurant);
        $this->manager->flush();


        return $this->json(
            [
                "Message" => "Restaurant resource created with  : {$restaurant->getId()}"
            ],
            status: Response::HTTP_CREATED
        );
    }

    #[Route("/{id}", name: "edit", methods: "PUT")]
    public function edit(int $id): Response
    {
        $restaurant = $this->repository->findOneBy(['id' => $id]);

        if (!$restaurant) {
            throw $this->createNotFoundException("No restaurant found with id {$id}");
        }

        $restaurant->setName("Name restaurant updated");
        $restaurant->setUpdatedAt(new DateTime());
        
        $this->manager->flush($restaurant);

        return $this->redirectToRoute('api_app_restaurant_show', ["id" => $restaurant->getId()]);

    }

    #[Route("/{id}", name: "delete", methods: "DELETE")]
    public function delete(int $id): Response
    {
        $restaurant = $this->repository->findOneBy(["id" => $id]);

        if (!$restaurant) {
            throw $this->createNotFoundException("No restaurant found with id : {$id}");
        }

        $this->manager->remove($restaurant);
        $this->manager->flush();

        return $this->json(
            [
                "message" =>    `The restaurant {$restaurant->getName()} 
                                with id : {$restaurant->getId()} was succefully deleted.`
            ],
            status: Response::HTTP_NO_CONTENT
        );
    }
}