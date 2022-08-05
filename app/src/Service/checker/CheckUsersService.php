<?php

namespace App\Service\checker;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckUsersService extends AbstractController
{
    public  function __construct(private readonly EntityManagerInterface $em){

    }
    public function check(){

        $currentTime =strtotime(date('Y-m-d H:i:s'));
        $users = $this->em->getRepository(User::class)->findBy(['isEmailVerified'=>false]);
        if (!empty($users)){
            foreach ($users as $user){
                dump($user);
                $userID = $user->getId();
                $expires = $user->getVerificationExpire();
                if ($expires<$currentTime){
                    $this->em->remove($user);
                    $this->em->flush();
                }

            }
        }

    }

}