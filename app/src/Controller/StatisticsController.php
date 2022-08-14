<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderedProducts;
use App\Entity\Product;
use App\Entity\User;
use Browser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em){

    }
    #[Route('/statistics', name: 'app_statistics',methods: 'GET')]
    public function statistics(): Response
    {
      $email = "dbirkas3@gmail.com";
      $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
      $orders = $this->em->getRepository(Order::class)->findBy(['userId'=>$user]);
      $dataAll = [];

      foreach ($orders as $order){
          $paid = 'no';
          $sent = 'no';

          if ($order->isPaid()){
              $paid = 'yes';
          }
          if ($order->isSent()){
              $sent = 'yes';
          }

          $data = [
              'orderNumber'=>$order->getId(),
              'orderNote'=>$order->getOrderNote(),
              'sent'=>$sent,
              'totalPrice'=>$order->getPrice(),
              'paid'=>$paid
          ];
          $dataAll[]= $data;
      }
      return $this->json($dataAll,Response::HTTP_OK);
    }

    #[Route('/statistics/order', name: 'app_statistics_order',methods: 'GET')]
    public function statisticsOrder(): Response
    {
        $id = 16;
        date_default_timezone_set("Europe/Belgrade");

//        $browser = new Browser();
//        dump($browser->isAol(), $browser->getPlatform(),$browser->getVersion(),$browser->getUserAgent(),$browser->isMobile(),$browser->isTablet());
        $orders = $this->em->getRepository(OrderedProducts::class)->findBy(['orderNumber'=>$id]);
        $dataAll = [];
        foreach ($orders as $order){

            $product = $this->em->getRepository(Product::class)->findOneBy(['id'=>$order->getProduct()]);
            $productName = $product->getName();
            $data=[
                'productName'=>$productName,
                'priceForOne'=>$order->getPrice(),
                'quantity'=>$order->getNumber()
            ];

            $dataAll[] = $data;

        }
        return $this->json($dataAll,Response::HTTP_OK);

    }

}
