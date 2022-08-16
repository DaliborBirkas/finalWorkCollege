<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Entity\FavoriteProduct;
use App\Entity\Product;
use App\Entity\Random;
use App\Entity\User;
use Doctrine\DBAL\Exception\DatabaseDoesNotExist;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\favorite\FavoriteService;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class FavoriteController extends AbstractController
{
    private JWTTokenManagerInterface $jwtManager;
    private TokenStorageInterface $tokenStorageInterface;

    public function __construct(TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager,
                                private  readonly EntityManagerInterface $em)
    {
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
    }

    #[Route('/favorite', name: 'app_favorite',methods: ['get','post'])]
    public function favorite(Request $request,FavoriteService $favoriteService): JsonResponse
    {
        // can be true, depends on the user
       // $favoriteService->setLikes($request);
        $authorizationHeader = $request->headers->get('Authorization');
        $authorizationHeaderArray = explode(' ', $authorizationHeader);
        $token = $authorizationHeaderArray[1] ?? null;
        $tokenParts = explode(".", $token);
        $tokenHeader = base64_decode($tokenParts[0]);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtHeader = json_decode($tokenHeader);
        $jwtPayload = json_decode($tokenPayload);

        //$jwtToken = $this->JWTEncoder->decode($token);


        $user = $this->getUser();
        $random = new Random();
        $random->setCheckerValue($jwtPayload->email);
        $this->em->persist($random);
        $this->em->flush();
       // dd($request->getContent());
        $data = json_decode($request->getContent());
       // exit(\Doctrine\Common\Util\Debug::dump($request->getContent()));
      //  $favoriteService->productLikeByUser($data,$user);
      //  $favoriteService->updateLikes($data);
        return $this->json('Success');
    }
    #[Route('/favorite/get', name: 'app_favorite_get')]
    public function favoriteGet(EntityManagerInterface $em): Response
    {
        // can be true, depends on the user
        $favorites = $em->getRepository(Favorite::class)->findBy(array(),array('likes'=>'DESC'),5);
        $response = [];
        foreach ($favorites as $favorite){

            $favoriteLikes = $favorite->getLikes();
            $product = $em->getRepository(Product::class)->findOneBy(['id'=>$favorite->getProduct()]);
            $productName = $product->getName();
            $data['likes'] = $favoriteLikes;
            $data['product'] = $productName;
            $data['picture'] = $product->getImage();
            $response[] = $data;

        }
        return $this->json($response);

    }
    #[Route('/favorite/my', name: 'app_favorite_my')]
    public function favoriteMy(Request $request,EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $user = $em->getRepository(User::class)->findOneBy(['email'=>'dbirkas3@gmail.com']);

        $favoriteProduct = $em->getRepository(FavoriteProduct::class)->findBy(['user'=>$user,'liked'=>1]);

        return $this->json($favoriteProduct);
    }
}
