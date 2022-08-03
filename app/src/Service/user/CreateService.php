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
use App\Service\MailerService;


class CreateService
{
    public function __construct( private  readonly EntityManagerInterface $em, private readonly UserPasswordHasherInterface $passwordHasher,
    private readonly MailerService $mailerService)
    {

    }
    public function createUser($data):array{
        $user = new User();

//        $name = $data->name;
//        $email = $data->email;
//        $surname = $data->surname;
//        $companyName = $data->companyName;
//        $address = $data->address;
//        $pib = $data->pib;
//        $city = $data->city;
//        $password = $data->password;
         //   $repeatedPassword = "";
        $errors = [];
        $name = "dsad";
        $email = "dbir2k34as332331@gmail.com";
        $surname = "perica";
        $companyName = "kompanija";
        $address = "Pera Perova 2";
        $pib = 12312343;
        $cityINT = 3;
        $password = "taraba";
        $repeatedPassword = "taraba";
        if (!ctype_alpha($name)){
            $errors['error-name'] ='Name can only contain letters';

        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
           $errors['error-email'] = 'Email is not valid';
        }
        if (!ctype_alpha($surname))
        {
                $errors['error-surname'] ='Surname can only contain letters';
        }
        if (!ctype_alpha($companyName))
        {
            $errors['error-company-name'] ='Surname can only contain letters';
        }
        if (ctype_digit($address))
        {
            $errors['error-address'] ='Address can not be only digits';
        }
        if(!(is_numeric($pib) && $pib > 0 && $pib == round($pib, 0)))
        {
            $errors['error-pib'] ='Pib has to be only integer';
        }
        if(!(is_numeric($cityINT) && $cityINT > 0 && $cityINT == round($cityINT, 0)))
        {
            $errors['error-city'] ='You have to send me city ID ';
        }
        if ($password !== $repeatedPassword){
            $errors['error-password'] = 'Passwords does not match';
        }
        if (empty($errors)){

            try {

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
            $errors['status'] = 'Created';
            }
            catch (\Exception $exception){
                 $errors['status'] = $exception->getMessage();
            }
        }
        if ($errors['status']='Created'){
            $this->mailerService->createEmail($email,$name);
        }
        return $errors;

    }
}