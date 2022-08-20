<?php

namespace App\Service\admin;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AddProductService extends AbstractController
{
    public function __construct( private  readonly EntityManagerInterface $em){

    }
    public function addProduct($data){
        $currentDate = date_create('now');
        $id = $data->categoryId;
        $name = $data->name;
        $description = $data->description;
        $price = $data->price;
        $balance = $data->balance;
        $image = $data->image;
        $category = $this->em->getRepository(Category::class)->findOneBy(['id'=>$id]);

        $product = new Product();
        $product->setCategory($category);
        $product->setName($name);
        $product->setDescription($description);
        $product->setPrice($price);
        $product->setBalance($balance);
        $product->setImage($image);
        $product->setDiscountPrice(0.00);
        $product->setDatum($currentDate);

        $this->em->persist($product);
        $this->em->flush();

    }
}