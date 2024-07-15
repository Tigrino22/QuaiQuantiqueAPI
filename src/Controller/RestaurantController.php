<?php

namespace App\Controller;

use DateTimeImmutable;
use DateTime;
use ramsey\Uuid;
use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;


#[Route("api/restaurant", name: "api_app_restaurant_")]
class RestaurantController extends AbstractController {

    public function __construct(
        private EntityManagerInterface $manager, 
        private RestaurantRepository $repository, 
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
        )
    {
    }

    #[Route("/{id}", name: "show", methods: "GET")]
    public function show(int $id): JsonResponse
    {

        $restaurant = $this->repository->findOneBy(['id' => $id]);

        if ($restaurant) {

            $responseData = $this->serializer->serialize($restaurant, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }

    #[Route("/", name: "new", methods: "POST")]
    public function new(Request $request): JsonResponse
    {
          
        $restaurant = $this->serializer->deserialize($request->getContent(), Restaurant::class, 'json');
        $restaurant->setUuid(UuidV4::uuid4());
        $restaurant->setCreatedAt(new DateTimeImmutable());


        // A stocker en base
        $this->manager->persist($restaurant);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($restaurant, 'json');
        $location = $this->urlGenerator->generate(
            "api_app_restaurant_show",
            ['id' => $restaurant->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ['location' => $location], true);

    }

    #[Route("/{id}", name: "edit", methods: "PUT")]
    public function edit(int $id, Request $request): Response
    {
        $restaurant = $this->repository->findOneBy(['id' => $id]);

        if ($restaurant){

            $restaurant = $this->serializer->deserialize(
                $request->getContent(),
                Restaurant::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $restaurant]
            );
            
            $restaurant->setUpdatedAt(new DateTime());
            
            $this->manager->flush();

            $responseData = $this->serializer->serialize($restaurant, 'json');
            $location = $this->urlGenerator->generate(
            "api_app_restaurant_show",
            ['id' => $restaurant->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

            return new JsonResponse($responseData, Response::HTTP_CREATED, ['location' => $location], true);

        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }

    #[Route("/{id}", name: "delete", methods: "DELETE")]
    public function delete(int $id): JsonResponse
    {
        $restaurant = $this->repository->findOneBy(["id" => $id]);
        
        if ($restaurant){

            $this->manager->remove($restaurant);
            $this->manager->flush();
    
            return $this->json(
                [
                    "message" =>    `The restaurant {$restaurant->getName()} 
                                    with id : {$restaurant->getId()} was succefully deleted.`
                ],
                status: Response::HTTP_NO_CONTENT
            );;

        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}