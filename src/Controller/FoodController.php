<?php

namespace App\Controller;

use App\Entity\Food;
use App\Repository\FoodRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/food', name: 'api_app_food_')]
class FoodController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $manager, 
        private FoodRepository $foodRepository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
        )
    {
        
    }

    #[Route("/{id}", name: "show", requirements: ["id" => "\d+"], methods: ["GET"])]
    public function show(int $id): JsonResponse
    {

        $food = $this->foodRepository->findOneBy(["id" => $id]);

        if ($food) {
            
            $responseData = $this->serializer->serialize($food, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);

        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }

    #[Route("/", name: "new", methods: "POST")]
    public function new(Request $request): JsonResponse
    {

        $food = $this->serializer->deserialize($request->getContent(), Food::class, 'json');
        $food->setUuid(UuidV4::uuid4());
        $food->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($food);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($food, 'json');
        $location = $this->urlGenerator->generate(
            'api_app_food_show',
            ['id' => $food->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ['location' => $location], true);
    }

    #[Route("/{id}", name: "edit", requirements: ["id" => "\d+"], methods: "PUT")]
    public function edit(Request $request, int $id): JsonResponse
    {

        $food = $this->foodRepository->findOneBy(["id" => $id]);


        if ($food) {
            
            $food = $this->serializer->deserialize(
                $request->getContent(), 
                Food::class, 
                'json',
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $food
                ]);
            $food->setUpdatedAt(new DateTime());

            $this->manager->flush();

            $responseData = $this->serializer->serialize($food, 'json');
            $location = $this->urlGenerator->generate(
                'api_app_food_show',
                [ 'id' => $food->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            
            return new JsonResponse($responseData, Response::HTTP_CREATED, ['location' => $location], 'true');
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        
    }

    #[Route("/{id}", name: "delete", requirements: ["id" => "\d+"], methods: "DELETE")]
    public function delete(int $id): JsonResponse
    {

        $food = $this->foodRepository->findOneBy(["id" => $id]);
        
        if ($food) {
            $this->manager->remove($food);
            $this->manager->flush();
    
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }
}
