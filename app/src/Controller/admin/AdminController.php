<?php

namespace App\Controller\admin;

use App\Entity\Order;
use App\Entity\OrderedProducts;
use App\Entity\User;
use App\Service\admin\SetRabatService;
use DateTime;
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
        $email = $data->email;
        $verifyUserService->verifyUser($email);
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
        $dataAll = [];
        foreach ($data as $row){
            $array=[
                'email'=>$row->getEmail(),
                'firstName'=>$row->getName(),
                'lastName'=>$row->getSurname(),
                'companyName'=>$row->getCompanyName(),
                'address'=>$row->getAddress()
            ];
            $dataAll[] = $array;
        }
        return $this->json($dataAll,RESPONSE::HTTP_OK);
    }
    //USERS FULL VERIFIED
    #[Route('/api/admin/users/emailVerifiedAdminNot', name: 'app_admin_users_emailVerifiedAdminNot')]
    public function adminVerification(Request $request):JsonResponse
    {
        $data = $this->em->getRepository(User::class)->findBy(['isEmailVerified'=>true,'is_verified'=>false]);
        return $this->json($data,RESPONSE::HTTP_OK);
    }
    //USERS FULL VERIFIED
    #[Route('/api/admin/users/adminDidNotVerify', name: 'app_admin_users_adminDidNotVerify',methods: 'get')]
    public function adminDidNotVerfiy(Request $request):JsonResponse
    {
        $data = $this->em->getRepository(User::class)->findBy(['is_verified'=>false]);
        return $this->json($data,RESPONSE::HTTP_OK);
    }

    #[Route('/api/admin/orders', name: 'app_admin_orders',methods: 'get')]
    public function orders(Request $request):JsonResponse
    {
        $orders = $this->em->getRepository(Order::class)->findAll();

        $dataAll = [];

        foreach ($orders as $order){
            $paid = 'ne';
            $sent = 'ne';

            if ($order->isPaid()){
                $paid = 'da';
            }
            if ($order->isSent()){
                $sent = 'da';
            }

            $data = [
                'orderNumber'=>$order->getId(),
                'orderNote'=>$order->getOrderNote(),
                'sent'=>$sent,
                'totalPrice'=>$order->getPrice(),
                'paid'=>$paid
            ];
            $dataAll[]= $data;
        }
        return $this->json($dataAll,Response::HTTP_OK);
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
    #[Route('/api/admin/user/rabat', name: 'app_admin_user_orders',methods: 'post')]
    public function userRabat(Request $request, SetRabatService $rabatService):JsonResponse
    {
        $data = json_decode($request->getContent());
        $user = $this->getUser();
        $rabatService->setRabat($data);
        return $this->json('Success',Response::HTTP_OK);
    }
    #[Route('/api/admin/orders/date', name: 'app_admin_user_orders_bydate')]
    public function ordersByDate(Request $request):JsonResponse
    {
         $info = json_decode($request->getContent());
        $infoDate = $info->date;

        $date = new DateTime($infoDate);

        $data = $this->em->getRepository(Order::class)->findBy(['orderDate'=>$date]);
        $dataAll = [];
        foreach ($data as $order){
            $paid = 'ne';
            $sent = 'ne';

            if ($order->isPaid()){
                $paid = 'da';
            }
            if ($order->isSent()){
                $sent = 'da';
            }

            $array = [
                'orderNumber'=>$order->getId(),
                'orderNote'=>$order->getOrderNote(),
                'sent'=>$sent,
                'totalPrice'=>$order->getPrice(),
                'paid'=>$paid
            ];
            $dataAll[]= $array;
        }

        return $this->json($dataAll,RESPONSE::HTTP_OK);
    }
}
