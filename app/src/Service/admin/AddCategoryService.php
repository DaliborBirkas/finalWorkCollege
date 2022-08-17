<?php

namespace App\Service\admin;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AddCategoryService extends AbstractController
{
    public function __construct( private  readonly EntityManagerInterface $em){

    }
    public function addCategory($data){

        $name = $data->name;
        $imageName = $data->image;

        $status = 'Success';
        $category = new Category();
        $category->setName($name);
        $category->setImage($imageName);
        $this->em->persist($category);
        $this->em->flush();


        return $status;
    }
}