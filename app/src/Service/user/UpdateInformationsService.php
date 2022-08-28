<?php

namespace App\Service\user;

use App\Entity\City;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class UpdateInformationsService
{
    public function __construct(private  readonly EntityManagerInterface $em,private readonly MailerInterface $mailerService){

    }
    public function update($data,$user){

        $address = $data->address;
        $cityINT = intval($data->city);
        $companyName = $data->companyName;
        $email = $data->email;
        $name = $data->name;
        $phoneNumber = intval($data->phoneNumber);
        $pib = $data->pib;
        $surname = $data->surname;

       // $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$newMail]);
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
        if (!is_int($phoneNumber)){
            $errors['error-phone'] ='Phone number not correct ';
        }

        if (empty($errors)){

            $city = $this->em->getRepository(City::class)->find($cityINT);
            if ($email!=$user->getEmail()){
                //dd('aa');
                $timeInt = strtotime(date('Y-m-d H:i:s'))+ 600;
                $user->setName($name);
                $user->setSurname($surname);
                $user->setCompanyName($companyName);
                $user->setPhoneNumber($phoneNumber);
                $user->setAddress($address);
                $user->setPib($pib);
                $user->setCity($city);
                $user->setEmail($email);
                $user->setisEmailVerified(0);
                $user->setverificationExpire($timeInt);
                $this->em->persist($user);
                $this->em->flush();

                $emailSend = (new TemplatedEmail())
                    ->to($email)
                    ->subject('Obaveštenje - Vaši podaci')
                    ->htmlTemplate('user/updateInfo.html.twig')
                    ->context([
                        'name'=>$name,
                    ]);
                $this->mailerService->send($emailSend);

                $emailSendVerification = (new TemplatedEmail())
                    ->to($email)
                    ->subject('Obaveštenje - Uspešna promena podataka')
                    ->htmlTemplate('mail/emailConfirmation.html.twig')
                    ->context([
                        'name'=>$name,
                        'emailAddress'=>$email,
                        'expires'=>$timeInt
                    ]);

                $this->mailerService->send($emailSendVerification);




                return 'Success';
            }
            else{

                $user->setName($name);
                $user->setSurname($surname);
                $user->setCompanyName($companyName);
                $user->setAddress($address);
                $user->setPib($pib);
                $user->setPhoneNumber($phoneNumber);
                $user->setCity($city);
              //  $user->setEmail($email);
                $this->em->persist($user);
                $this->em->flush();
                $email = (new TemplatedEmail())
                    ->to($email)
                    ->subject('Obavestenje - Vasi podaci')
                    ->htmlTemplate('user/updateInfo.html.twig')
                    ->context([
                        'name'=>$name,
                    ]);
                $this->mailerService->send($email);
                return 'Success';
            }



        }
        else{
            return 'Aborted';
        }

    }
}