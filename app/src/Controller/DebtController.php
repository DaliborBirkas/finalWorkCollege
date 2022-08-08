<?php

namespace App\Controller;

use App\Entity\Debt;
use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DebtController extends AbstractController
{
    public  function __construct(private readonly EntityManagerInterface $em){

    }

    #[Route('/debt', name: 'app_debt')]
    public function index(): Response
    {

        $users = $this->em->getRepository(User::class)->findAll();
        foreach ($users as $user){
            $total = 0;
            $orders = $this->em->getRepository(Order::class)->findBy(['userId'=> $user,'paid'=>false]);
            foreach ($orders as $order){
                $total = $total + $order->getPrice();
            }
            $debt = $this->em->getRepository(Debt::class)->findOneBy(['user'=>$user]);
            if ($debt){
                $debt->setAmount($total);
                $this->em->persist($debt);
                $this->em->flush();

            }
            else{
                $debt = new Debt();
                $debt->setUser($user);
                $debt->setAmount($total);
                $this->em->persist($debt);
                $this->em->flush();
            }



        }
        return $this->json('Updated debts');
    }
}
