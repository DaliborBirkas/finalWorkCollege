<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Entity\FavoriteProduct;
use App\Entity\Product;
use App\Entity\Random;
use App\Entity\User;
use Doctrine\DBAL\Exception\DatabaseDoesNotExist;
use Doctrine\ORM\EntityManagerInterface;
//use http\Client;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Notifier\Bridge\Twilio\TwilioTransport;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\favorite\FavoriteService;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Twilio\Rest\Client;


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

    #[Route('/api/favorite', name: 'app_favorite',methods: ['get','post'])]
    public function favorite(Request $request,FavoriteService $favoriteService): JsonResponse
    {


        $user = $this->getUser();

        $data = json_decode($request->getContent());
       // exit(\Doctrine\Common\Util\Debug::dump($request->getContent()));
        $favoriteService->productLikeByUser($data,$user);
        $favoriteService->updateLikes($data);
        return $this->json('Success');
    }
    #[Route('/favorite/get', name: 'app_favorite_get', methods: 'get')]
    public function favoriteGet(EntityManagerInterface $em): Response
    {
        // can be true, depends on the user
        $favorites = $em->getRepository(Favorite::class)->findBy(array(),array('likes'=>'DESC'));
        $response = [];
        foreach ($favorites as $favorite){

            $favoriteLikes = $favorite->getLikes();
            if ($favoriteLikes!=0){
                $product = $em->getRepository(Product::class)->findOneBy(['id'=>$favorite->getProduct()]);
                $productName = $product->getName();
                $data['likes'] = $favoriteLikes;
                $data['product'] = $productName;
                $data['picture'] = $product->getImage();
                $response[] = $data;
            }


        }
        return $this->json($response, Response::HTTP_OK);

    }
    #[Route('/api/favorite/my', name: 'app_favorite_my',methods: 'get')]
    public function favoriteMy(Request $request,EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $favoriteProduct = $em->getRepository(FavoriteProduct::class)->findBy(['user'=>$user,'liked'=>1]);
        $data = [];
        foreach ($favoriteProduct as $value){
            $product = $value->getProduct();
            $array = [];
            $array['name'] = $product->getName();
            $array['category'] = $product->getCategory()->getName();
            $array['description'] = $product->getDescription();
            $array['price'] = $product->getPrice();
            $array['image'] = $product->getImage();
            $array['discountPrice'] = $product->getDiscountPrice();
            $data[] = $array;
        }

        return $this->json($data, Response::HTTP_OK);
    }
}
