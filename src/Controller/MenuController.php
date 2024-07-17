<?php

/**
 * This file contains the CRUD for Restaurant's menu.
 * 
 * @category Controller_CRUD
 * @package  Nothing
 * @author   Valentin Legris <valentin.legris@outlook.com>
 * @license  MIT www.abba.com
 * @link     Nothing
 */
namespace App\Controller;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use App\Repository\RestaurantRepository;
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

#[Route("api/menu", name: "api_app_menu_")]
/**
 * MenuController
 * CRUD for Restaurant's menu.
 * 
 * @category Class_file
 * @package  Nothing
 * @author   Valentin Legris <valentin.legris@outlook.com>
 * @license  MIT www.abba.com
 * @link     Nothing
 */
class MenuController extends AbstractController
{
    
    /**
     * Construct
     *
     * @param mixed $manager
     * @param mixed $restaurantRepository
     */
    public function __construct(
        private EntityManagerInterface $manager, 
        private RestaurantRepository $restaurantRepository, 
        private MenuRepository $menuRepository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
    ) {
        
    }

    #[Route("/{id}", name: "show", methods: ["GET"], requirements: ["id" => "\d+"])]    
    /**
     * Show
     *
     * @param  mixed $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $menu = $this->menuRepository->findOneBy(["id" => $id]);

        if($menu){

            $responseData = $this->serializer->serialize($menu, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);

        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }

    #[Route("/", name: "new", methods: ["POST"])]    
    /**
     * Create and redirect to show route
     *
     * @return JsonResponse
     */
    public function new(Request $request): JsonResponse
    {
        
        $menu = $this->serializer->deserialize($request->getContent(), Menu::class, 'json');

        $menu->setCreatedAt(new DateTimeImmutable());
        $menu->setUuid(UuidV4::uuid4());

        $this->manager->persist($menu);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($menu, 'json');
        $location = $this->urlGenerator->generate(
            'api_app_menu_show',
            ['id' => $menu->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ['location' => $location], true);
    }



    #[Route("/{id}", name: "edit", methods: ["PUT"], requirements: ["id" => "\d+"])]    
    /**
     * Edit and redirect to shown route
     *
     * @param  mixed $id
     * @return Response
     */
    public function edit(Request $request, int $id): Response
    {
        $menu = $this->menuRepository->findOneBy(["id" => $id]);

        if($menu){
            $menu = $this->serializer->deserialize(
                $request->getContent(),
                Menu::class,
                'json',
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $menu
                ]
                );
            $menu->setUpdatedAt(new DateTime());

            $this->manager->flush();

            $responseData = $this->serializer->serialize($menu, 'json');
            $location = $this->urlGenerator->generate(
                'api_app_menu_show',
                [
                    'id' => $menu->getId()
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            return new JsonResponse($responseData, Response::HTTP_CREATED, ['location' => $location], true);
            
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }

    #[Route("/{id}", name: "delete", methods: ["DELETE"], requirements: ["id" => "\d+"])]    
    /**
     * Delete
     *
     * @param  mixed $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $menu = $this->menuRepository->findOneBy(["id" => $id]);

        if($menu){
            $this->manager->remove($menu);
            $this->manager->flush();
    
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }
}
