<?php

namespace App\Service\user;

use App\Entity\User;
use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use mysql_xdevapi\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\EmailValidator;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Flex\Response;


class CreateService
{
    public function __construct( private  readonly EntityManagerInterface $em, private readonly UserPasswordHasherInterface $passwordHasher,
                               )
    {

    }
    public function createUser($data):bool{
        try {


        $user = new User();

//        $name = $data->name;
//        $email = $data->email;
//        $surname = $data->surname;
//        $companyName = $data->companyName;
//        $address = $data->address;
//        $pib = $data->pib;
//        $city = $data->city;
//        $password = $data->password;

        $name = "djuka";
        $email = "random@email.com";
        $surname = "perica";
        $companyName = "kompanija";
        $address = "Aksentija 2";
        $pib = 4433;
        $cityINT = 2;
        $password = "taraba";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
           return false;
        }

        $cityString = $this->em->getRepository(City::class)->find($cityINT);

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $password
        );
        $user->setName($name);
        $user->setRoles([]);
        $user->setEmail($email);
        $user->setPassword($hashedPassword);
        $user->setSurname($surname);
        $user->setCompanyName($companyName);
        $user->setAddress($address);
        $user->setPib($pib);
        $user->setCity($cityString);
        $user->setPhoneNumber('+394565456545');
        $user->setIsVerified(false);
        $this->em->persist($user);
        $this->em->flush();
            }
            catch (\Exception $exception){
            throw new  Exception();
            }

        return true;

    }
}