<?php

namespace App\Service\admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;

class VerifyUserService extends AbstractController
{
    public function __construct( private  readonly EntityManagerInterface $em,private readonly MailerInterface $mailer){

    }
    public function verifyUser($email){

        $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);

        if ($user){
            $emailUser = $user->getEmail();
            $nameUser = $user->getName();
            $pibUser = $user->getPib();

            $emaill = (new TemplatedEmail())
                ->to($emailUser)
                ->subject('Verifikacija')
                ->htmlTemplate('admin/verifiedByAdmin.html.twig')
                ->context([
                    'name'=>$nameUser,
                    'pib'=>$pibUser,
                ]);
            $this->mailer->send($emaill);
        }

        $user->setIsVerified(true);

        $this->em->persist($user);
        $this->em->flush();


    }

}