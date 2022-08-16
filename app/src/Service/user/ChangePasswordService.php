<?php

namespace App\Service\user;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ChangePasswordService
{
    public function __construct(private readonly UserRepository $userRepository,private readonly UserPasswordHasherInterface $passwordHasher,
                                private  readonly EntityManagerInterface $em){

    }
    public function updatePassword($data,$user){
        //$email = $data->email;

        $email = "dbirkas3@gmail.com";
        $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
        $password = "test45";
        $repeatedPassword = "test45";
        if ($password !== $repeatedPassword){
            return 'Aborted';
        }
        else{
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $password
            );
            $this->userRepository->upgradePassword($user,$hashedPassword);
            return 'Success';
        }
    }


}