<?php

namespace App\Service\admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class SetRabatService
{
    public function __construct( private  readonly EntityManagerInterface $em){

    }
    public function setRabat($data){
        $rabat = $data->discount;
        $email = $data->email;
        $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
        $user->setRabat($rabat);
        $this->em->persist($user);
        $this->em->flush();
    }
}