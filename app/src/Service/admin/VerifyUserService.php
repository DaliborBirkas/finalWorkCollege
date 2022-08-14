<?php

namespace App\Service\admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VerifyUserService extends AbstractController
{
    public function __construct( private  readonly EntityManagerInterface $em){

    }
    public function verifyUser($id){
        $id = 128;

        $user = $this->em->getRepository(User::class)->findOneBy(['id'=>$id]);

        $user->setIsVerified(true);

        $this->em->persist($user);
        $this->em->flush();


    }

}