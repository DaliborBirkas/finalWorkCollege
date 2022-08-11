<?php

namespace App\Service\user;

use App\Entity\Debt;
use App\Entity\Order;
use App\Entity\OrderedProducts;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;


class OrderService extends AbstractController
{
    public function __construct( private  readonly EntityManagerInterface $em,
                                 private readonly MailerInterface $mailerService)
    {

    }
    public function createOrder($data){

        date_default_timezone_set("Europe/Belgrade");

//        $email = ;
//        $price = ;
//        $orderNote = ;


        $email = "nikola@gmail.com";

        $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
        $userRabat = $user->getRabat();
        $userName= $user->getName();
        $userVerifiedMail = $user->isIsEmailVerified();
        $userVerifiedAdmin = $user->isIsVerified();

        if ($userVerifiedMail){
            if ($userVerifiedAdmin){

                $debt = $this->em->getRepository(Debt::class)->findOneBy(['user'=>$user]);
                if (empty($debt)){
                    $currentDate = date_create('now');
                    $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
                    $price = 1111.22;
                    if ($userRabat!=0){
                        $price = $price - ($price/$userRabat);
                    }

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

               // $amount = $debt->getAmount();
               // $amount = $amount + $totalPrice;

                $amount = 11;



                if ($amount<60000){

                    $currentDate = date_create('now');
                    $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
                    $price = 1111.22;
                  //  $price = $price - ($price/$user);
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
                else{
                    return 'Amount not valid';
                }


            }
            else{
                $email = (new Email())
                    ->to($email)
                    ->subject('Verifikacija')
                    ->html("
                    <h2>Postovani  $userName</h2>
                    <h4> Porudzbina</h4>   
                    <p>Ne mozete da kreirate porudzbinu jer vas admin nije jos verifikovao. Molim vas sacekajte</p>
                    <br>
                    <h4>Kozna galenterija</h4>
                    <h4>Dalibor</h4>
                    <h4>+38564655456</h4>                
                    ");
                $this->mailerService->send($email);
            }

        }
        else{
            $email = (new Email())
                ->to($email)
                ->subject('Verifikacija')
                ->html("
                    <h2>Postovani  $userName</h2>
                    <h4> Porudzbina</h4>   
                    <p>Ne mozete da kreirate porudzbinu jer vasa email adresa nije verifikovana. Molim vas odradite verifikaciju.</p>
                    <br>
                    <h4>Kozna galenterija</h4>
                    <h4>Dalibor</h4>
                    <h4>+38564655456</h4>                
                    ");
            $this->mailerService->send($email);

        }

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