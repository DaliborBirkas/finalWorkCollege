<?php

namespace App\Controller\admin;

use App\Entity\Order;
use App\Entity\OrderedProducts;
use App\Entity\User;
use App\Service\admin\SetRabatService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\admin\VerifyUserService;
use App\Service\admin\AddCategoryService;
use App\Service\admin\AddProductService;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    public function __construct(private  readonly EntityManagerInterface $em){

    }
    //#[IsGranted('ROLE_ADMIN')]
    #[Route('/api/admin/users/verify', name: 'app_admin_users_verify')]
    public function verifyUser(Request $request, VerifyUserService $verifyUserService):JsonResponse
    {
        $data = json_decode($request->getContent());
       // $id = $data->id;
        $id =1;
        $verifyUserService->verifyUser($id);
        return $this->json('Success',RESPONSE::HTTP_OK);
    }
    #[Route('/api/admin/category/add', name: 'app_admin_category_add', methods: 'post')]
    public function addCategory(Request $request,AddCategoryService $addCategoryService):JsonResponse{

        $data = json_decode($request->getContent());

        $status= $addCategoryService->addCategory($data);

        return $this->json($status,RESPONSE::HTTP_OK);
    }
    #[Route('/api/admin/product/add', name: 'app_admin_product_add', methods: 'post')]
    public function addProduct(Request $request, AddProductService $addProductService):JsonResponse{
        $data = json_decode($request->getContent());
        $addProductService->addProduct($data);
        return $this->json('Success',RESPONSE::HTTP_OK);
    }

    // ALL USERS
    #[Route('/api/admin/users', name: 'app_admin_users')]
    public function users(Request $request):JsonResponse
    {
        $data = $this->em->getRepository(User::class)->findAll();
        return $this->json($data,RESPONSE::HTTP_OK);
    }

    //USERS VERIFIED EMAIL
    #[Route('/api/admin/users/emailVerified', name: 'app_admin_users_emailVerified')]
    public function emailVerified(Request $request):JsonResponse
    {
        $data = $this->em->getRepository(User::class)->findBy(['isEmailVerified'=>true]);
        return $this->json($data,RESPONSE::HTTP_OK);
    }

    // USERS EMAIL NOT VERIFIED
    #[Route('/api/admin/users/emailNotVerified', name: 'app_admin_users_emailNotVerified')]
    public function emailNotVerified(Request $request):JsonResponse
    {
        $data = $this->em->getRepository(User::class)->findBy(['isEmailVerified'=>false]);
        return $this->json($data,RESPONSE::HTTP_OK);
    }

    //USERS FULL VERIFIED
    #[Route('/api/admin/users/verified', name: 'app_admin_users_verified')]
    public function verified(Request $request):JsonResponse
    {
        $data = $this->em->getRepository(User::class)->findBy(['isEmailVerified'=>true,'is_verified'=>true]);
        return $this->json($data,RESPONSE::HTTP_OK);
    }
    //USERS FULL VERIFIED
    #[Route('/api/admin/users/emailVerifiedAdminNot', name: 'app_admin_users_emailVerifiedAdminNot')]
    public function adminVerification(Request $request):JsonResponse
    {
        $data = $this->em->getRepository(User::class)->findBy(['isEmailVerified'=>true,'is_verified'=>false]);
        return $this->json($data,RESPONSE::HTTP_OK);
    }

    #[Route('/api/admin/orders', name: 'app_admin_orders')]
    public function orders(Request $request):JsonResponse
    {
        $data = $this->em->getRepository(Order::class)->findAll();
        return $this->json($data,RESPONSE::HTTP_OK);
    }
    #[Route('/api/admin/orders/order', name: 'app_admin_orders_order')]
    public function order(Request $request):JsonResponse
    {
       // $info = json_decode($request->getContent());
        //$id = $info->id;

        $data = $this->em->getRepository(OrderedProducts::class)->findBy(['orderNumber'=>1]);
        return $this->json($data,RESPONSE::HTTP_OK);
    }

    #[Route('/api/admin/user/orders', name: 'app_admin_user_orders')]
    public function userOrders(Request $request):JsonResponse
    {
        // $info = json_decode($request->getContent());
        //$id = $info->id;

        $data = $this->em->getRepository(Order::class)->findBy(['userId'=>1]);
        return $this->json($data,RESPONSE::HTTP_OK);
    }
    #[Route('/api/admin/user/rabat', name: 'app_admin_user_orders')]
    public function userRabat(Request $request, SetRabatService $rabatService):JsonResponse
    {
        $data = json_decode($request->getContent());
        $user = $this->getUser();
        $rabatService->setRabat($data,$user);
        return $this->json('Success',Response::HTTP_OK);
    }
}
