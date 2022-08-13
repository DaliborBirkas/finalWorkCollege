<?php

namespace App;


class AppKernel
{
    public function registerBundles()
    {

        $bundle = array(

            new Cron\CronBundle\CronCronBundle(),
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
        );



        return $bundle;
    }
    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }
}