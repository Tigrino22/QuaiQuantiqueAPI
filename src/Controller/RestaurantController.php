<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/restaurant", name: "api_app_restaurant_")]
class RestaurantController extends AbstractController {

    #[Route("/{id}", name: "show", methods: "GET")]
    public function show()
    {
        return new Response();
    }

    #[Route("/{id}", name: "edit", methods: "PUT")]
    public function edit()
    {
        return new Response();
    }

    #[Route("/create", name: "create", methods: "POST")]
    public function create()
    {
        return new Response();
    }

    #[Route("/{id}", name: "delete", methods: "DELETE")]
    public function delete()
    {
        return new Response();
    }
}