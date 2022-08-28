<?php

namespace App\Service\user;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ChangePasswordService
{
    public function __construct(private readonly UserRepository $userRepository,private readonly UserPasswordHasherInterface $passwordHasher,
                                private  readonly EntityManagerInterface $em,private readonly MailerInterface $mailerService){

    }
    public function updatePassword($data,$user){

        $password = $data->newPassword;
        $repeatedPassword = $data->repeatedPassword;
        $email = $user->getEmail();

        $oldPw = $data->password;
        $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
        $userName = $user->getName();
        if (!password_verify($oldPw,$user->getPassword())){
            return 'Aborted';
        }
        if ($password !== $repeatedPassword){
            return 'Aborted';
        }
        else{
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $password
            );
            $this->userRepository->upgradePassword($user,$hashedPassword);

            $email = (new TemplatedEmail())
                ->to($email)
                ->subject('Obavestenje - Lozinka')
                ->htmlTemplate('user/newPassword.html.twig')
                ->context([
                    'name'=>$userName,
                ]);
            $this->mailerService->send($email);

            return 'Success';
        }
    }


}