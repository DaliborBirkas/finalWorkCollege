<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    public  function __construct(private readonly EntityManagerInterface $em){

    }
    #[Route('/category', name: 'app_category')]
    public function index(): Response
    {
        $category = $this->em->getRepository(Category::class)->findAll();

        return $this->json($category);
    }

}
