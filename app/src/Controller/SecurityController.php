<?php
namespace App\Controller;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityController extends AbstractController
{

    #[Route('/login', name: 'app_login')]
    public function login(Request $request):JsonResponse{

        $user = $this->getUser();
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json([
                'error' => 'Invalid login request: check that the Content-Type header is "application/json".'
            ], 400);
        }
        return $this->json([

                'user-email' => $this->getUser() ? $user->getRoles() : null,


                ]
        );
    }

}