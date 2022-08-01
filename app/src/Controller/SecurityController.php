<?php
namespace App\Controller;
use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Flex\Response;


class SecurityController extends AbstractController
{

    #[Route('/login', name: 'app_login',methods: 'POST')]
    public function login(Request $request,LoggerInterface $logger,
                          IriConverterInterface $iriConverter,AuthenticationUtils $authenticationUtils){

        $user = $this->getUser();
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $logger->info('aaa');
            return $this->json([

                'error' => 'Invalid login request: check that the Content-Type header is "application/json".'
            ], 400);
        }

        return $this->json([
                'status'=> 'lgtm',
                'user-email' => $this->getUser() ? $user->getRoles() : null,
                'Location'=>$iriConverter->getIriFromItem($this->getUser()),
                'req'=>$request->getContent(),
                'lastusername'=>$authenticationUtils->getLastUsername()
                ]
        );

    }
    #[Route('/logout',name: 'app_logout')]
    public function logout(){
        throw new \Exception('should not be reached');

    }

}