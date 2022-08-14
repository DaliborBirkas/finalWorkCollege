<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;



class MailerService extends AbstractController
{
    public function __construct(private readonly MailerInterface $mailer)
    {

    }
    public function createEmail($to,$name,$expires){
        $email = (new TemplatedEmail())
            ->to($to)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Uspesna registracija')
            ->htmlTemplate('mail/mail.html.twig')
            ->context([
                'name'=>$name,
                'emailAddress'=>$to,
                'expires'=>$expires
            ]);

        $this->mailer->send($email);
    }




}