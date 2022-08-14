<?php

namespace App\Service\admin;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AddCategoryService extends AbstractController
{
    public function __construct( private  readonly EntityManagerInterface $em){

    }
    public function addCategory($name,){
        $status = 'Success';
        if (!ctype_alpha($name)){
            $status ='Declined';

        }
        else{
            $category = new Category();
            $category->setName($name);
            $this->em->persist($category);
            $this->em->flush();
        }

        return $status;
    }
}