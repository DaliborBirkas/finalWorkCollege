<?php

namespace App\Service\user;

use App\Entity\Order;
use App\Entity\OrderedProducts;
use App\Entity\Product;
use App\Entity\User;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class OrderService extends AbstractController
{
    public function __construct( private  readonly EntityManagerInterface $em, private readonly UserPasswordHasherInterface $passwordHasher,
                                 private readonly MailerService $mailerService)
    {

    }
    public function createOrder($data){

        date_default_timezone_set("Europe/Belgrade");

//        $email = ;
//        $price = ;
//        $orderNote = ;

        $email = "korisnik2234@gmail.com";
        $currentDate = date_create('now');
        $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
        $price = 1111.22;
        $orderNote = "Racun please";

        $order = new Order();

        $order->setUserId($user);
        $order->setOrderNote($orderNote);
        $order->setOrderDate($currentDate);
        $order->setSent(false);
        $order->setPrice($price);
        $order->setPaid(false);

        $this->em->persist($order);
        $this->em->flush();

        return $order->getId();

    }

    public function storeOrderedProducts($id){

        $productId = 1;

        $order = $this->em->getRepository(Order::class)->findOneBy(['id'=>$id]);
        $product = $this->em->getRepository(Product::class)->findOneBy(['id'=>$productId]);

        $total = 1111.44;
        $quantity = 3;

        $orderProducts = new OrderedProducts();
        $orderProducts->setOrderNumber($order);
        $orderProducts->setProduct($product);
        $orderProducts->setPrice($total);
        $orderProducts->setNumber($quantity);

        $this->em->persist($orderProducts);
        $this->em->flush();

    }
}