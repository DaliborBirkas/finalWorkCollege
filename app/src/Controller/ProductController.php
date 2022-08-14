<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    public  function __construct(private readonly EntityManagerInterface $em){

    }
    #[Route('/product', name: 'app_product',methods: 'GET')]
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
    #[Route('/product/findByCategory', name: 'app_product_find_by_category',methods: 'GET')]
    public function findByCategory(Request $request): Response
    {
        $info = json_decode($request->getContent());
        $category = $info->category;

        $products =$this->em->getRepository(Product::class)->findBy(['category'=>$category]);

        $data = [];

        $categoryArray = [];

        foreach ($products as $key=>$product){
            $categoryArray['id'] = $product->getId();
            $categoryArray['category']= $product->getCategory()->getName();
            $categoryArray['balance'] = $product->getBalance();
            $categoryArray['name']= $product->getName();
            $categoryArray['description'] = $product->getDescription();
            $categoryArray['price'] = $product->getPrice();
            $categoryArray['image'] = $product->getImage();
            $categoryArray['discountPrice'] = $product->getDiscountPrice();
            $data[] = $categoryArray;
        }
        return $this->json($data);
    }

    #[Route('/product/five', name: 'app_product_five',methods: 'GET')]
    public function five(): Response
    {
//        $products=$this->em->getRepository(Product::class)->findBy(array(),array('discountPrice'=>'DESC'),5);
//        $topFiveProducts = [];
//        foreach ($products as $product){
//            $eachProduct = [];
//            $productName = $product->getName();
//            $productDescription = $product->getDescription();
//            $productOldPrice = $product->getPrice();
//            $productBalance = $product->getBalance();
//            $productNewPrice = $product->getDiscountPrice();
//
//            $category = $this->em->getRepository(Category::class)->findOneBy(['id'=>$product->getCategory()]);
//            $categortName = $category->getName();
//
//            $eachProduct['name'] = $productName;
//            $eachProduct['description'] = $productDescription;
//            $eachProduct['oldPrice'] = $productOldPrice;
//            $eachProduct['newPrice'] = $productNewPrice;
//            $eachProduct['balance'] = $productBalance;
//            $eachProduct['categoryName'] = $categortName;
//            $topFiveProducts[] =$eachProduct;
//
//        }
//        dd($topFiveProducts);
        $products =$this->em->getRepository(Product::class)->findBy(array(),array('price'=>'ASC'),5);
        $data = [];
        $categoryArray = [];
        foreach ($products as $key=>$product){
            $categoryArray['id'] = $product->getId();
            $categoryArray['category']= $product->getCategory()->getName();
            $categoryArray['balance'] = $product->getBalance();
            $categoryArray['name']= $product->getName();
            $categoryArray['description'] = $product->getDescription();
            $categoryArray['price'] = $product->getPrice();
            $categoryArray['image'] = $product->getImage();
            $categoryArray['discountPrice'] = $product->getDiscountPrice();
            $data[] = $categoryArray;
        }
        return $this->json($data);
    }
}
