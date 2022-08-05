<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    public  function __construct(private readonly EntityManagerInterface $em){

    }
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        $products =(array)$this->em->getRepository(Product::class)->findAll();
        $data = [];
        $counter = 0;
        $categoryArray = [];
        foreach ($products as $key=>$product){
          $categoryArray['id'] = $product->getId();
            $categoryArray['category']= $product->getCategory()->getName();
            $categoryArray['balance'] = $product->getBalance();
            $categoryArray['name']= $product->getName();
            $categoryArray['description'] = $product->getDescription();
            $categoryArray['price'] = $product->getPrice();
            $categoryArray['image'] = $product->getImage();
            $data[] = $categoryArray;
        }
        return $this->json($data);
    }
}
