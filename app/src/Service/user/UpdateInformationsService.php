<?php

namespace App\Service\user;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UpdateInformationsService
{
    public function __construct(private  readonly EntityManagerInterface $em){

    }
    public function update($data,$user){
        // TO DO  READ DATA FROM JSON
        $email = $data->email;

        $email = "dbirkas3@gmail.com";
        $name = "Jankeza";
        $surname = "Jankelaaas";
        $companyName= "adasdsa";
        $phoneNumber = 1231;
        $address = "ulicascsa";
        $pib = 312;
        $password = "test1";
        $repeatedPassword = "test1";
        $cityINT = 1;

        $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
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
        if (is_numeric($companyName))
        {
            $errors['error-company-name'] ='Company can only contain letters';
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

        }

    }
}