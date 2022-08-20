<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ResetPassword;

class ResetPasswordController extends AbstractController
{
    public  function __construct(private readonly EntityManagerInterface      $em,private readonly MailerInterface $mailer,
                                 private readonly UserPasswordHasherInterface $passwordHashed){

    }
    #[Route('/reset/password', name: 'app_reset_password')]
    public function sendRequest(Request $request)
    {
        $data = json_decode($request->getContent());
        $rand = substr(md5(microtime()),rand(0,26),5);
        $resetPw = new ResetPassword();
        // 10 minutes then expires
        $timeInt = strtotime(date('Y-m-d H:i:s'))+ 600;
        $message= "";
       // $email = "pero68505@gmail.com";
        $email = $data->email;

        $emailReset =$this->em->getRepository(ResetPassword::class)->findBy(['email'=>$email]);
        $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
        if (empty($user)){
            return $this->json([
                'status'=>'User with this email does not exists'
            ]);
        }

        else{
            $userName = $user->getName();
            $userLastname = $user->getSurname();
        if (empty($emailReset)){
            $resetPw->setEmail($email);
            $resetPw->setExpire($timeInt);
            $resetPw->setValidator($rand);
            $this->em->persist($resetPw);
            $this->em->flush();
            $email = (new Email())
                ->to($email)
                ->subject('Promena lozinke')
                ->html("
                    <h2>Postovani $userName $userLastname</h2><br>
                    <h4>Vas zahtev za novu lozinku je kreiran</h4>   
                    <p>Vas kod za promenu lozinke je<b> $rand</b></p>
                    <p>Kliknite na klikni me kako bi ste promenili lozinku</p>
                    <a href='http://localhost:4200/restartPassword'>Klikni me</a>
                    <p>Zahtev je validan 10 minuta</p>
                    <br>

                    <h4>Kozna galenterija</h4>
                    <h4>Dalibor</h4>
                    <h4>+38564655456</h4>                
                    ");
            $this->mailer->send($email);
            return $this->json([
                'status'=>'Reset password request successful'
            ]);
        }
        else{
            foreach ($emailReset as $value){
                if ($value->getExpire()<$timeInt){
                    $rand2 = substr(md5(microtime()),rand(0,26),5);
                    $value->setExpire($timeInt);
                    $value->setValidator($rand2);
                    $this->em->persist($value);
                    $this->em->flush();
                    $email = (new Email())
                        ->to($email)
                        ->subject('Promena lozinke')
                        ->html("
                    <h2>Postovani  $userName $userLastname </h2><br>
                    <h4> Kreirali smo novi zahtev za promenu lozinke</h4>   
                    <p>Vas kod je $rand2</p>
                    <p>Zahtev je validan 10 minuta, po isteku 10 minuta potrebno je obnoviti zahtev</p>
                     <p>Kliknite na klikni me kako bi ste promenili lozinku</p>
                    <a href='http://localhost:4200/restartPassword'>Klikni me</a>
                    <br>
                    <h4>Kozna galenterija</h4>
                    <h4>Dalibor</h4>
                    <h4>+38564655456</h4>                
                    ");
                    $this->mailer->send($email);
                    return $this->json([
                        'status'=>'Updating time and validator, request exists but already expired '
                    ]);
                }
            }

        }}

    }
    #[Route('/reset/password/change', name: 'app_reset_password_change', methods: 'post')]
    public function resetPassword(Request $request)
    {

        $timeInt = strtotime(date('Y-m-d H:i:s'));
        $data = json_decode($request->getContent());
        $email = $data->email;
        $validator = $data->kod;
        $password = $data->password;
        $passwordRepeated = $data->repeatedPassword;

        $emailReset =$this->em->getRepository(ResetPassword::class)->findOneBy(['email'=>$email,'validator'=>$validator]);
        if (empty($emailReset)){
            return $this->json([
                'status'=>'Email and validator are wrong!'
            ]);
        }

        if ($emailReset->getExpire()<$timeInt){
            $this->em->remove($emailReset);
            $this->em->flush();
            $email = (new Email())
                ->to($email)
                ->subject('Lozinka')
                ->html("
                    <h2>Postovani  </h2><br>
                    <h4>Vas prethodni zahtev za lozinku je istekao.Posaljite novi</h4>   
                    <p>Vas novi kod je <b>/b></p>
                    <br>

                    <h4>Kozna galenterija</h4>
                    <h4>Dalibor</h4>
                    <h4>+38564655456</h4>                
                    ");
            $this->mailer->send($email);
            return $this->json([
                'status'=>'Time expired, send new request'
            ]);

        }
        if ($emailReset->getExpire()>$timeInt){
            if($password == $passwordRepeated){
                $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
                $hashedPw = $this->passwordHashed->hashPassword($user,$password);
                $user->setPassword($hashedPw);
                $userName = $user->getName();
                $this->em->persist($user);
                $this->em->remove($emailReset);
                $this->em->flush();

                $email = (new Email())
                    ->to($email)
                    ->subject('Lozinka')
                    ->html("
                    <h2>Postovani $userName </h2><br>
                    <h4>Vasa lozinka je azurirana</h4>   
                    <br>
                    <h4>Kozna galenterija</h4>
                    <h4>Dalibor</h4>
                    <h4>+38564655456</h4>                
                    ");
                $this->mailer->send($email);


                return $this->json([
                    'status'=>'Password updated'
                ]);
            }
            else{
                return $this->json([
                    'status'=>'Password does not match'
                ]);
            }

        }

    }
}
