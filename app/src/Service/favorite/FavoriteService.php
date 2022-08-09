<?php

namespace App\Service\favorite;

use App\Entity\Favorite;
use App\Entity\Product;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FavoriteService extends AbstractController
{
    public function __construct( private  readonly EntityManagerInterface $em){

    }
    public function setLikes($data){

        // can be true
        $like = false;
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

}