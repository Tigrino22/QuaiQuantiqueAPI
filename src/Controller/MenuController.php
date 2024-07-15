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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function __construct(private EntityManagerInterface $manager, private RestaurantRepository $restaurantRepository, 
        private MenuRepository $menuRepository
    ) {
        
    }

    #[Route("/", name: "new", methods: ["POST"])]    
    /**
     * Create
     *
     * @return Response
     */
    public function new(): Response
    {
        
        $menu = new Menu();

        $this->manager->persist($menu);
        $this->manager->flush();

        return $this->json(
            [
            "message" => "Menu was created with uuid : {$menu->getUuid()}"
            ]
        );
    }

    #[Route("/{id}", name: "show", methods: ["GET"], requirements: ["id" => "\d+"])]    
    /**
     * Show
     *
     * @param  mixed $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $menu = $this->menuRepository->findOneBy(["id" => $id]);

        if($menu){

            return $this->json(
                [
                "message" => "Menu was found with uuid : {$menu->getUuid()}"
                ]
            );

        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }

    #[Route("/{id}", name: "edit", methods: ["PUT"], requirements: ["id" => "\d+"])]    
    /**
     * Edit
     *
     * @param  mixed $id
     * @return Response
     */
    public function edit(int $id): Response
    {
        $menu = $this->menuRepository->findOneBy(["id" => $id]);

        if($menu){
            $menu->setUpdatedAt(new DateTime());

            return $this->json(
                [
                "message" => "Menu was modifier with uuid : {$menu->getUuid()}, new name : {$menu->getTitle()} at {$menu->getUpdatedAt()->format('Y-m-d H:i:s')}"
                ]
            );
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
    
            return $this->json(
                [
                "message" => "Menu with uuid {$menu->getUuid()} was deleted."
                ]
            );
        }
        
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }
}
