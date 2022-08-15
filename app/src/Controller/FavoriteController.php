<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Entity\Product;
use Doctrine\DBAL\Exception\DatabaseDoesNotExist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\favorite\FavoriteService;

class FavoriteController extends AbstractController
{
    #[Route('/favorite', name: 'app_favorite')]
    public function index(Request $request,FavoriteService $favoriteService): Response
    {
        // can be true, depends on the user
       // $favoriteService->setLikes($request);
        $user = $this->getUser();
        $data = json_decode($request->getContent());
        $favoriteService->productLikeByUser($data,$user);
        $favoriteService->updateLikes($data);
        return $this->json('Success');
    }
    #[Route('/favorite/get', name: 'app_favorite_get')]
    public function favorite(EntityManagerInterface $em): Response
    {
        // can be true, depends on the user
        $favorites = $em->getRepository(Favorite::class)->findBy(array(),array('likes'=>'DESC'),10);
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
}
