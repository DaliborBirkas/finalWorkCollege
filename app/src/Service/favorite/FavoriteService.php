<?php

namespace App\Service\favorite;

use App\Entity\Favorite;
use App\Entity\FavoriteProduct;
use App\Entity\Product;
use App\Entity\Random;
use App\Entity\User;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FavoriteService extends AbstractController
{
    public function __construct( private  readonly EntityManagerInterface $em){

    }
    public function setLikes($data){

        // can be true
        $like = true;
        //id product
        $id = 1;

        $product = $this->em->getRepository(Product::class)->findOneBy(['id'=>$id]);

        $favorite = $this->em->getRepository(Favorite::class)->findOneBy(['product'=>$product]);
        if (empty($favorite)){
            if ($like==false){

                $favoriteNew = new Favorite();

                $favoriteNew->setProduct($product);
                $favoriteNew->setLikes(0);
                $this->em->persist($favoriteNew);
                $this->em->flush();
            }
            else{

                $favoriteNew = new Favorite();

                $favoriteNew->setProduct($product);
                $favoriteNew->setLikes(1);
                $this->em->persist($favoriteNew);
                $this->em->flush();
            }

        }
        else{
            $likes = $favorite->getLikes();

            if ($likes==0){

                if ($like==true){

                    $favorite->setLikes(1);
                    $this->em->persist($favorite);
                    $this->em->flush();

                }
            }
            else{
                if ($like==false){

                    $likes= $likes -1;
                    $favorite->setLikes($likes);
                    $this->em->persist($favorite);
                    $this->em->flush();
                }
                else{
                    $likes= $likes +1;
                    $favorite->setLikes($likes);
                    $this->em->persist($favorite);
                    $this->em->flush();
                }
            }
        }


    }

    public function productLikeByUser($data, $userr){

        $productId = $data->id;
        $product = $this->em->getRepository(Product::class)->findOneBy(['id'=>$productId]);

        $favoriteProduct = $this->em->getRepository(FavoriteProduct::class)->findOneBy(['product'=>$product,'user'=>$userr]);
        if (empty($favoriteProduct)){

            $newFavoriteProduct = new FavoriteProduct();
            $newFavoriteProduct->setProduct($product);
            $newFavoriteProduct->setUser($userr);
            $newFavoriteProduct->setLiked(1);

            $this->em->persist($newFavoriteProduct);
            $this->em->flush();
        }
        else{
            $liked = $favoriteProduct->getLiked();
            if ($liked==1){
                $favoriteProduct->setLiked(0);
                $this->em->persist($favoriteProduct);
                $this->em->flush();
            }
            if ($liked==0){
                $favoriteProduct->setLiked(1);
                $this->em->persist($favoriteProduct);
                $this->em->flush();
            }
        }
    }
    public function updateLikes($dataa){
        $productId =  $dataa->id;
       //$productId = 1;
        $product = $this->em->getRepository(Product::class)->findOneBy(['id'=>$productId]);

        $favoriteProduct = $this->em->getRepository(FavoriteProduct::class)->findBy(['product'=>$product,'liked'=>1]);
        $count = 0;
        foreach ($favoriteProduct as $array){
            $count++;
        }
        $favorite = $this->em->getRepository(Favorite::class)->findOneBy(['product'=>$product]);
        if ($count!=0){

            if (empty($favorite)){
                dump('if');
                $newFavorite = new Favorite();
                $newFavorite->setProduct($product);
                $newFavorite->setLikes($count);
                $this->em->persist($newFavorite);
                $this->em->flush();
            }
            else{
                dump('else');
                $favorite->setLikes($count);
                $this->em->persist($favorite);
                $this->em->flush();
            }
        }
        else{
            if (empty($favorite)){
                $newFavorite = new Favorite();
                $newFavorite->setProduct($product);
                $newFavorite->setLikes($count);
                $this->em->persist($newFavorite);
                $this->em->flush();
            }
            else{
                $favorite->setLikes($count);
                $this->em->persist($favorite);
                $this->em->flush();
            }
        }

    }

}