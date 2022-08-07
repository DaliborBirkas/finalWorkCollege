<?php

namespace App\Controller;

use App\Entity\City;
use App\Service\CityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CityController extends AbstractController
{
    public function __construct(private readonly CityService $cityService)
    {
    }
    #[Route('/insert/city', name: 'app_insert_city',methods: 'POST')]
    public function insert(): JsonResponse
    {
        $this->cityService->insertCity();
        return $this->json([
            'status'=>'insert done']);

    }

    #[Route('/get/city', name: 'app_get_city', methods: 'GET')]
    public function get(): JsonResponse
    {
        $city = $this->cityService->getCity();
        return $this->json($city, Response::HTTP_OK);
    }

}
