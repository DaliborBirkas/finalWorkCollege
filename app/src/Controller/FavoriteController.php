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

    /**
     * @throws \Twilio\Exceptions\TwilioException
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    #[Route('/api/favorite', name: 'app_favorite',methods: ['get','post'])]
    public function favorite(Request $request,FavoriteService $favoriteService): JsonResponse
    {
        // can be true, depends on the user
       // $favoriteService->setLikes($request);
//        $authorizationHeader = $request->headers->get('Authorization');
//        $authorizationHeaderArray = explode(' ', $authorizationHeader);
//        $token = $authorizationHeaderArray[1] ?? null;
//        $tokenParts = explode(".", $token);
//        $tokenHeader = base64_decode($tokenParts[0]);
//        $tokenPayload = base64_decode($tokenParts[1]);
//        $jwtHeader = json_decode($tokenHeader);
//        $jwtPayload = json_decode($tokenPayload);
        //$jwtToken = $this->JWTEncoder->decode($token);
        $sid = 'AC2a4a1de0e344520c50dd5cc1df681ff4';
        $token = '52a0dcd7e06e9432ff0a88645f4c24c9';
        $client = new Client($sid, $token);
        //$m = new SmsMessage()
// Use the client to do fun stuff like send text messages!
        $client->messages->create(
        // the number you'd like to send the message to
            '+381616967616',
            [
                // A Twilio phone number you purchased at twilio.com/console
                'from' => '+14054517789',
                // the body of the text message you'd like to send
                'body' => 'Hakovani ste'
            ]
        );

        $user = $this->getUser();

        $data = json_decode($request->getContent());
       // exit(\Doctrine\Common\Util\Debug::dump($request->getContent()));
        $favoriteService->productLikeByUser($data,$user);
        $favoriteService->updateLikes($data);
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
    #[Route('/api/favorite/my', name: 'app_favorite_my')]
    public function favoriteMy(Request $request,EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $favoriteProduct = $em->getRepository(FavoriteProduct::class)->findBy(['user'=>$user,'liked'=>1]);

        return $this->json($favoriteProduct);
    }
}
