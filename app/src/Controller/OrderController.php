<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\user\OrderService;

class OrderController extends AbstractController
{
    public function __construct( private readonly OrderService $orderService)
    {
    }
    #[Route('/order/create', name: 'app_order')]
    public function index(Request $request): Response
    {
       $data = json_decode($request->getContent());

       $order =  $this->orderService->createOrder($data);

      if(empty($order)){
          return $this->json('Can not create order',Response::HTTP_OK);
      }
      else{
          $this->orderService->storeOrderedProducts($order);
          return $this->json('Success',Response::HTTP_OK);
      }

    }
}
