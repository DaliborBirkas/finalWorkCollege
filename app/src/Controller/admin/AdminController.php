<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\admin\VerifyUserService;
use App\Service\admin\AddCategoryService;
use App\Service\admin\AddProductService;

class AdminController extends AbstractController
{
    #[Route('/admin/verifyUser', name: 'app_admin_verify_user')]
    public function verifyUser(Request $request, VerifyUserService $verifyUserService)
    {
        $verifyUserService->verifyUser(3);
        return $this->json('Success',RESPONSE::HTTP_OK);
    }
    #[Route('/admin/add/category', name: 'app_admin_add_category')]
    public function addCategory(Request $request,AddCategoryService $addCategoryService){
        $name = "Kategorija 2";
        $status= $addCategoryService->addCategory($name);
        return $this->json($status,RESPONSE::HTTP_OK);
    }
    #[Route('/admin/add/product', name: 'app_admin_add_product')]
    public function addProduct(Request $request, AddProductService $addProductService){
        $data = json_decode($request->getContent());

        $idCategory = 2;
        $name = "proizvod";
        $description = "opis";
        $price = 222.99;
        $balance = 2444;
        $image = "333.jpg";

        $addProductService->addProduct($idCategory,$name,$description,$price,$balance,$image);
        return $this->json('Success',RESPONSE::HTTP_OK);
    }


}
