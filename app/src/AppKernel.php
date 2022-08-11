<?php

namespace App;


class AppKernel
{
    public function registerBundles()
    {

        $bundle = array(

            new Cron\CronBundle\CronCronBundle(),
        );


        return $bundle;
    }
}