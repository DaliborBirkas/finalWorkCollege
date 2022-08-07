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
    #[Route('/reset/password', name: 'app_reset_password', methods: 'POST')]
    public function sendRequest(Request $request)
    {
        $rand = substr(md5(microtime()),rand(0,26),5);
        $resetPw = new ResetPassword();
        // 10 minutes then expires
        $timeInt = strtotime(date('Y-m-d H:i:s'))+ 600;
        $message= "";
        $email = "dalibor@mail.com";

        $data = json_decode($request->getContent());
        $email = $data->email;
        $emailReset =$this->em->getRepository(ResetPassword::class)->findBy(['email'=>$email]);
        $user = $this->em->getRepository(User::class)->findBy(['email'=>$email]);
        if (empty($user)){
            return $this->json([
                'status'=>'User with this email does not exists'
            ]);
        }
        else{
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
                    <h2>Postovani  </h2><br>
                    <h4>Vas zahtev za novu lozinku je kreiran</h4>   
                    <p>Vas kod za promenu lozinke je<b>$rand</b></p>
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
                    <h2>Postovani  </h2><br>
                    <h4> Kreirali smo novi zahtev</h4>   
                    <p>Vas kod je $rand2</p>
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
    #[Route('/reset/password/change', name: 'app_reset_password_change')]
    public function resetPassword(Request $request)
    {
        $email = "dalibor@mail.com";
        $validator = "81dd9";
        $timeInt = strtotime(date('Y-m-d H:i:s'));

        $password = "test";
        $passwordRepeated = "test";

        $data = json_decode($request->getContent());
        $email = $data->email;
        $validator = $data->validator;
        $password = $data->password;
        $passwordRepeated = $data->passwordRepeated;

        $emailReset =$this->em->getRepository(ResetPassword::class)->findOneBy(['email'=>$email,'validator'=>$validator]);
        if (empty($emailReset)){
            return $this->json([
                'status'=>'Email does not exists'
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
