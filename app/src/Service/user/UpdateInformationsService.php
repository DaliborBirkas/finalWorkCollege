<?php

namespace App\Service\user;

use Doctrine\ORM\EntityManagerInterface;

class UpdateInformationsService
{
    public function __construct(private  readonly EntityManagerInterface $em){

    }
    public function update($data,$user){

    }
}