<?php

namespace App\Controller\marketing;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Marketing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[ApiResource()]
class MarketingController extends AbstractController
{
    public  function __construct(private readonly EntityManagerInterface $em){

    }
    #[Route('/marketing', name: 'app_marketing',methods: 'post' )]
    public function index(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent());
        $email = $data->email;
        $marketing = new Marketing();
        $marketing->setEmail($email);
        $this->em->persist($marketing);
        $this->em->flush();
        return  $this->json('Success',Response::HTTP_OK);
    }
}
