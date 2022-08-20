<?php

namespace App\Controller;

use App\Entity\Random;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\user\ChangePasswordService;
use App\Service\user\OrderService;
use App\Service\user\UpdateInformationsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\user\CreateService;
use App\Service\user\VerifyEmailService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
    public function __construct(private readonly CreateService $createService,private readonly VerifyEmailService $verifyEmailService,
                                private  readonly EntityManagerInterface $em,private readonly MailerInterface $mailer,
                                private readonly OrderService $orderService){

    }
    #[Route('/user/create', name: 'app_user_create',methods: 'POST')]
    public function registration(Request $request): JsonResponse
    {

        $dataDecoded = json_decode($request->getContent());
        $data = $this->createService->createUser($dataDecoded);
        if (empty($data)){
            $data = 'Created';
        }

        return $this->json([
            'status'=>$data
        ]);
    }
    #[Route('/user/verify_email/{email}/{expires}', name: 'app_user_verify_email',methods: ['GET','POST'])]
    public function verifiyEmail(Request $request)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $request->attributes->get('email')]);
       $verification = $this->verifyEmailService->verifyEmail($request);
    $userName = $user->getName();

    if ($verification == "verification success"){
        $email = (new Email())
            ->to($request->attributes->get('email'))
            ->subject('Verifikacija')
            ->html("
                    <h2>Postovani $userName </h2><br>
                    <h4>Uspesno ste verifikovali vas nalog</h4>   
                     
                    <br>

                    <h4>Kozna galenterija</h4>
                    <h4>Dalibor</h4>
                    <h4>+38564655456</h4>                
                    ");
        $this->mailer->send($email);
        return $this->redirect('http://localhost:4200/');

    }
    if ($verification == "you are already verified"){
        $email = (new Email())
            ->to($request->attributes->get('email'))
            ->subject('Verifikacija')
            ->html("
                    <h2>Postovani $userName </h2><br>
                    <h4>Vas nalog je vec verifikovan</h4>   
                     
                    <br>

                    <h4>Kozna galenterija</h4>
                    <h4>Dalibor</h4>
                    <h4>+38564655456</h4>                
                    ");
        $this->mailer->send($email);
        return $this->redirect('http://localhost:4200/');
    }
    if ($verification == "could not verify email"){
        $email = (new Email())
            ->to($request->attributes->get('email'))
            ->subject('Verifikacija')
            ->html("
                    <h2>Postovani $userName </h2><br>
                    <h4>Doslo je do greske, pokusajte opet da verifikujete nalog.</h4> 
                    <h5>Mozda je vreme predlozeno za verifikaciju isteklo</h5>  
                     
                    <br>

                    <h4>Kozna galenterija</h4>
                    <h4>Dalibor</h4>
                    <h4>+38564655456</h4>                
                    ");
        $this->mailer->send($email);
        return $this->redirect('http://localhost:4200/');
    }
    else{
        return $this->redirect('http://localhost:4200/');
    }
    }
    #[Route('api/me', methods: 'GET')]
    public function getCurrentUser(Request $request): JsonResponse
    {
       // $data = json_decode($request->getContent());
        $user = $this->getUser();
//        $random = new Random();
//        $random->setCheckerValue($user->getId());
//        $this->em->persist($random);
//        $this->em->flush();
       // $order =  $this->orderService->createOrder($data,$user);
        return $this->json($user, Response::HTTP_OK);
    }


    #[Route('/api/user/update', name: 'app_user_update', methods: 'patch')]
    public function updateInformation(Request $request, UpdateInformationsService $updateInformationsService)
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent());
        $message = $updateInformationsService->update($data,$user);
        return $this->json($message, Response::HTTP_OK);
    }


    #[Route('/api/user/newpassword', name: 'app_user_new_password')]
    public function setNewPassword(Request $request,ChangePasswordService $changePasswordService)
    {
        $data = json_decode($request->getContent());
        $user = $this->getUser();
        $message = $changePasswordService->updatePassword($data,$user);
        return $this->json($message,Response::HTTP_OK);

    }
    #[Route('/emails', name: 'emails')]
    public function emails(UserRepository $userRepository)
    {
        dd($userRepository->emails());
        return $this->json($message,Response::HTTP_OK);

    }



}
