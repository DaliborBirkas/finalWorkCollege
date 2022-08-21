<?php

namespace App\Service\admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class SetRabatService
{
    public function __construct( private  readonly EntityManagerInterface $em){

    }
    public function setRabat($data,$user){
        $rabat = $data->rabat;
//        $rabat = 15;
//        $user = $this->em->getRepository(User::class)->findOneBy(['id'=>1]);
        $user->setRabat($rabat);
        $this->em->persist($user);
        $this->em->flush();
    }
}