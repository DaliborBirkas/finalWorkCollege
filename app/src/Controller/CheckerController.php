<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckerController extends AbstractController
{
    public  function __construct(private readonly EntityManagerInterface $em){

    }
    #[Route('/checker', name: 'app_checker')]
    public function index()
    {
        $currentTime = strtotime(date('Y-m-d H:i:s'));
        $users = $this->em->getRepository(User::class)->findBy(['isEmailVerified'=>false]);
        if (!empty($users)){
            foreach ($users as $user){
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
