<?php

namespace App\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\City;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Json;

class CityService extends AbstractController
{
    public function __construct( private readonly EntityManagerInterface $em)
    {
    }
    public  function insertCity(){

        $cityJson = json_decode(file_get_contents(__DIR__ . '/../jsonData/cities.json'));
        foreach ($cityJson as $array){

                $city = new City();
                $city->setName($array->city);
                $city->setPostalCode($array->postal_code);
                $this->em->persist($city);
                $this->em->flush();

        }

    }

    public function getCity() :array{
        $city = $this->em->getRepository(City::class)->findAll();


        // returns an array of Product object
        $cities = [];
        $number = 0 ;
        foreach ($city as $value){

           $cities[$number]['id'] = $value->getId();
            $cities[$number]['name'] = $value->getName();
           $cities[$number]['postal_code'] = $value->getPostalCode();
           $number++;
        }
        return $cities;
    }

}