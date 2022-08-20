<?php

namespace App\Service\user;

use App\Entity\City;
use App\Entity\Debt;
use App\Entity\Order;
use App\Entity\OrderedProducts;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Snappy\Pdf;
use Twilio\Rest\Client;


class OrderService extends AbstractController
{
    private $pdf;
    private $projectDir;
    public function __construct( private  readonly EntityManagerInterface $em,
                                 private readonly MailerInterface $mailerService, private readonly Environment $twig,
                                 Pdf $pdf,string $projectDir  )
    {
        $this->pdf = $pdf;
        $this->projectDir = $projectDir;
    }
    public function createOrder($data,$user){
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);

        date_default_timezone_set("Europe/Belgrade");

//        $email = ;
//        $price = ;
//        $orderNote = ;

        //$email = $data->email;
        // $user = $this->getUser();
        // $email = $user->getEmail();
        $email = "dbirkas3@gmail.com";
        //$email = $user->getEmail();

        $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
        $userRabat = $user->getRabat();
        $userName= $user->getName();
        $userSurname = $user->getSurname();
        $userCompany = $user->getCompanyName();
        $userPib = $user->getPib();
        $userAddress = $user->getAddress();
        $userCity = $this->em->getRepository(City::class)->findOneBy(['id'=>$user->getCity()])->getName();
        $cityPostalCode = $this->em->getRepository(City::class)->findOneBy(['id'=>$user->getCity()])->getPostalCode();
        $userPhone = $user->getPhoneNumber();

        $userVerifiedMail = $user->isIsEmailVerified();
        $userVerifiedAdmin = $user->isIsVerified();

        if ($userVerifiedMail){
            if ($userVerifiedAdmin){

                $debt = $this->em->getRepository(Debt::class)->findOneBy(['user'=>$user]);
                if (empty($debt)){
                    $currentDate = date_create('now');
                    $currentDateForPdf = date('d.m.Y.');
                    $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
                    $price = 1000.22;
                    if ($userRabat!=0){
                        $price = $price - ($price/$userRabat);
                    }
                    $sid = 'AC2a4a1de0e344520c50dd5cc1df681ff4';
                    $token = '52a0dcd7e06e9432ff0a88645f4c24c9';
                    $client = new Client($sid, $token);
                    //$m = new SmsMessage()
// Use the client to do fun stuff like send text messages!
                    $client->messages->create(
                    // the number you'd like to send the message to
                        '+381616967616',
                        [
                            // A Twilio phone number you purchased at twilio.com/console
                            'from' => '+14054517789',
                            // the body of the text message you'd like to send
                            'body' => 'Hakovani ste'
                        ]
                    );

                    $orderNote = "Racun please";

                    $order = new Order();

                    $order->setUserId($user);
                    $order->setOrderNote($orderNote);
                    $order->setOrderDate($currentDate);
                    $order->setSent(false);
                    $order->setPrice($price);
                    $order->setPaid(false);
                    $this->em->persist($order);
                    $this->em->flush();

                    $idOrder = $order->getId();

//                    $html = $this->renderView('order/pdf.html.twig',[
//                        'polje'=>$order
//                    ]);
                    $html = $this->twig->render('order/pdf.html.twig',[
                        'name'=>$userName,
                        'idOrder'=>$idOrder,
                        'date'=>date('d.m.Y. H:i:s'),
                        'surname'=>$userSurname,
                        'company'=>$userCompany,
                        'pib'=>$userPib,
                        'address'=>$userAddress,
                        'city'=>$userCity,
                        'phone'=>$userPhone,
                        'cityPostal'=>$cityPostalCode,
                        'orderDate'=>$currentDateForPdf

                    ]);
                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();
//                    $dompdf->stream("mypdf.pdf", [
//                        "Attachment" => true
//                    ]);
                    $output = $dompdf->output();
                    $publicDirectory = $this->projectDir. '/public/documents/';
                    $pdfFilepath = $publicDirectory.'predracun broj'.$idOrder.'.pdf';
                    file_put_contents($pdfFilepath,$output);

                  //  $pdf = $this->pdf->generateFromHtml($html);
                   // $path = 'which wkhtmltopdf';
                   // $pdf->binary = $path;

                    $email = (new TemplatedEmail())
                        ->to($email)
                        //->cc('cc@example.com')
                        //->bcc('bcc@example.com')
                        //->replyTo('fabien@example.com')
                        //->priority(Email::PRIORITY_HIGH)
                        ->subject('Vasa narudzba')
                        ->htmlTemplate('order/email.html.twig')
                        ->context([
                            'name'=>$userName,
                            'idOrder'=>$idOrder
//                            'name'=>$name,
//                            'emailAddress'=>$to,
//                            'expires'=>$expires
                        ])
                        //->attach(sprintf('your-order-%s.pdf',date('Y-m-d')));
                        ->attachFromPath($pdfFilepath);



                    $this->mailerService->send($email);


                    return $order->getId();
                }

               // $amount = $debt->getAmount();
               // $amount = $amount + $totalPrice;

                $amount = 11;



                if ($amount<60000){
                    $sid = 'AC2a4a1de0e344520c50dd5cc1df681ff4';
                    $token = '52a0dcd7e06e9432ff0a88645f4c24c9';
                    $client = new Client($sid, $token);
                    //$m = new SmsMessage()
// Use the client to do fun stuff like send text messages!
                    $client->messages->create(
                    // the number you'd like to send the message to
                        '+381616967616',
                        [
                            // A Twilio phone number you purchased at twilio.com/console
                            'from' => '+14054517789',
                            // the body of the text message you'd like to send
                            'body' => 'Hakovani ste'
                        ]
                    );
                    $currentDate = date_create('now');
                    $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
                    $price = 900.22;
                  //  $price = $price - ($price/$user);
                    $orderNote = "Racun please";

                    $order = new Order();

                    $order->setUserId($user);
                    $order->setOrderNote($orderNote);
                    $order->setOrderDate($currentDate);
                    $order->setSent(false);
                    $order->setPrice($price);
                    $order->setPaid(false);
                    $this->em->persist($order);
                    $this->em->flush();

                    return $order->getId();
                }
                else{
                    return 'Amount not valid';
                }


            }
            else{
                $email = (new Email())
                    ->to($email)
                    ->subject('Verifikacija')
                    ->html("
                    <h2>Postovani  $userName</h2>
                    <h4> Porudzbina</h4>   
                    <p>Ne mozete da kreirate porudzbinu jer vas admin nije jos verifikovao. Molim vas sacekajte</p>
                    <br>
                    <h4>Kozna galenterija</h4>
                    <h4>Dalibor</h4>
                    <h4>+38564655456</h4>                
                    ");
                $this->mailerService->send($email);
            }

        }
        else{
            $email = (new Email())
                ->to($email)
                ->subject('Verifikacija')
                ->html("
                    <h2>Postovani  $userName</h2>
                    <h4> Porudzbina</h4>   
                    <p>Ne mozete da kreirate porudzbinu jer vasa email adresa nije verifikovana. Molim vas odradite verifikaciju.</p>
                    <br>
                    <h4>Kozna galenterija</h4>
                    <h4>Dalibor</h4>
                    <h4>+38564655456</h4>                
                    ");
            $this->mailerService->send($email);

        }

    }

    public function storeOrderedProducts($id){

        $productId = 1;

        $order = $this->em->getRepository(Order::class)->findOneBy(['id'=>$id]);
        $product = $this->em->getRepository(Product::class)->findOneBy(['id'=>$productId]);

        $total = 1111.44;
        $quantity = 3;

        $orderProducts = new OrderedProducts();
        $orderProducts->setOrderNumber($order);
        $orderProducts->setProduct($product);
        $orderProducts->setPrice($total);
        $orderProducts->setNumber($quantity);

        $this->em->persist($orderProducts);
        $this->em->flush();

    }
}