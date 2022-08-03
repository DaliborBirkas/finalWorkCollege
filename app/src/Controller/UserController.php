<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\user\CreateService;

use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(private readonly CreateService $createService){

    }
    #[Route('/user/create', name: 'app_user_create')]
    public function registration(Request $request): JsonResponse
    {

//        if ($this->createService->createUser(json_decode($request->getContent()))){
//            return $this->json([
//                'status'=>'created'
//            ]);
//        }
//        else{
//            return $this->json([
//                'status'=>'not created'
//            ]);
//        }

        $data = $this->createService->createUser(json_decode($request->getContent()));

        return $this->json($data);
    }
    #[Route('/user/verify_email', name: 'app_user_verify_email')]
    public function verifiyEmail(Request $request): JsonResponse
    {
        return $this->json([
            'status'=>'jasam'
            ]);
    }
}
