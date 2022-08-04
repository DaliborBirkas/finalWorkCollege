<?php

namespace App\Service\user;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class VerifyEmailService extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {

    }
    public function verifyEmail(Request $request){
        $email = $request->attributes->get('email');
        $expires = intval($request->attributes->get('expires'));
        $currentTime = strtotime(date('Y-m-d H:i:s'));
        $info = [];
        try {
            if($user = $this->em->getRepository(User::class)->findOneBy(['email' => $email,'verificationExpire'=>$expires,'isEmailVerified'=>false])){
                if ($currentTime<$expires){
                    $user->setIsEmailVerified(true);
                    $this->em->persist($user);
                    $this->em->flush();
                    return 'verification success';

                }
            }
            if($user = $this->em->getRepository(User::class)->findOneBy(['email' => $email,'verificationExpire'=>$expires,'isEmailVerified'=>true])){
                return 'you are already verified';

            }
        }
        catch (\Exception $exception){

        return "could not verify email";

        }

    }

}