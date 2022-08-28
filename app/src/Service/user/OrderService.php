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

        $products = $data->products;
        $orderNote = $data->orderNote;

        if (empty($orderNote)){
            $orderNote = "-";
        }
        $userEmail = $user->getEmail();
        $userRabat = $user->getRabat();
        $rabatPdf = $userRabat;
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
                    if ($userRabat!=0){
                        $userRabat = $userRabat/100;
                    }

                    $total = 0;
                    foreach ($products as $value){
                        $product = $this->em->getRepository(Product::class)->findOneBy(['id'=>$value->id]);
                        $quantityDatabase = $product->getBalance();
                        $quantityOrder = $value->balance;
                        $quantityOrder =1;
                        $discountPrice = $product->getDiscountPrice();
                        $regularPrice = $product->getPrice();
                        if ($quantityOrder > $quantityDatabase){

                        }else{

                            if ($discountPrice!=0.00){
                                $total = $total + ($discountPrice*$quantityOrder);
                            }
                            else{
                                $total = $total + ($regularPrice*$quantityOrder);
                            }
                        }

                    }
                    $totalWithoutRabat = round($total,2);

                    $total = round( $total - ($total*$userRabat),2);

                    if ($total>0 && $total<100000){

                    $order = new Order();

                    $order->setUserId($user);
                    $order->setOrderNote($orderNote);
                    $order->setOrderDate($currentDate);
                    $order->setSent(false);
                    $order->setPrice($total);
                    $order->setPaid(false);
                    $this->em->persist($order);
                    $this->em->flush();
                    $idOrder = $order->getId();

                    $sid = 'AC2a4a1de0e344520c50dd5cc1df681ff4';
                    $token = '52a0dcd7e06e9432ff0a88645f4c24c9';
                    $client = new Client($sid, $token);
                    //$m = new SmsMessage()
                    $client->messages->create(
                    // the number you'd like to send the message to
                        '+381616967616',
                        [
                            // A Twilio phone number you purchased at twilio.com/console
                            'from' => '+14054517789',
                            // the body of the text message you'd like to send
                            'body' => 'Postovani, vasa porudzbina je evidentirana. Broj porudzbine je '.$idOrder.'. Vasa kozna galenterija. '
                        ]
                    );

                    foreach ($products as $value ){
                        $product = $this->em->getRepository(Product::class)->findOneBy(['id'=>$value->id]);
                        $productName = $product->getName();
                        $quantityDatabase = $product->getBalance();
                        $quantityOrder = $value->balance;
                        $quantityOrder = 1;
                        $discountPrice = $product->getDiscountPrice();
                        $regularPrice = $product->getPrice();
                        if ($quantityOrder > $quantityDatabase){
                            $email1 = (new Email())
                                ->to($user->getEmail())
                                ->subject('Proizvod - Kolicina')
                                ->html('<p>Postovani '.$userName.',<br> '.$productName.' je izbacen iz porudzbine jer trenutna dostupna kolicina je '.$quantityDatabase.',
                                a vi ste hteli da porucite '.$quantityOrder.'. Ukoliko zelite da porucite proizvod napravite narudzbu sa dostupnom kolicinom.<br>
                                Kozna galenterija<br>
                                Dalibor<br>
                                Developer & Customer support<br>
                                +385646646464664</p>');
                            $this->mailerService->send($email1);
                        }else{
                            $product->setBalance($quantityDatabase-$quantityOrder);
                            $this->em->persist($product);
                            $this->em->flush();
                            $orderedProduct = new OrderedProducts();
                            $orderedProduct->setOrderNumber($order);
                            $orderedProduct->setProduct($product);
                            $orderedProduct->setNumber($quantityOrder);
                            if ($discountPrice!=0.00){
                                $orderedProduct->setPrice($discountPrice);
                            }
                            else{
                                $orderedProduct->setPrice($regularPrice);
                            }
                            $this->em->persist($orderedProduct);
                            $this->em->flush();
                        }

                    }
                    $pdfProducts = $this->em->getRepository(OrderedProducts::class)->findBy(['orderNumber'=>$order]);
                    $pdfArray = [];
                    foreach ($pdfProducts as $pdfProduct){
                        $infos = [];
                        $infos['product'] = $pdfProduct->getProduct()->getName();
                        $infos['category'] = $pdfProduct->getProduct()->getCategory()->getName();
                        $infos['price'] = $pdfProduct->getPrice();
                        $infos['quantity'] = $pdfProduct->getNumber();
                        $pdfArray[]= $infos;
                    }

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
                        'orderDate'=>$currentDateForPdf,
                        'rabat'=>$rabatPdf,
                        'totalWithoutRabat'=>$totalWithoutRabat,
                        'total'=>$total,
                        'products'=>$pdfArray,
                        'note'=>$orderNote

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


                    $email = (new TemplatedEmail())
                        ->to($user->getEmail())

                        ->subject('Vasa narudzba')
                        ->htmlTemplate('order/email.html.twig')
                        ->context([
                            'name'=>$userName,
                            'idOrder'=>$idOrder,
                            'orderDate'=>$currentDateForPdf,
                            'rabat'=>$rabatPdf,
                            'totalWithoutRabat'=>$totalWithoutRabat,
                            'total'=>$total,
                            'products'=>$pdfArray,
                            'note'=>$orderNote
//                            'name'=>$name,
//                            'emailAddress'=>$to,
//                            'expires'=>$expires
                        ])
                        //->attach(sprintf('your-order-%s.pdf',date('Y-m-d')));
                        ->attachFromPath($pdfFilepath);

                    $newDebt = new Debt();
                    $newDebt->setUser($user);
                    $newDebt->setAmount($total);
                    $this->em->persist($newDebt);
                    $this->em->flush();

                    $this->mailerService->send($email);

                    return 'Success';
                    }
                    else{
                        $message = (new Email())
                            ->to($user->getEmail())
                            ->subject('Dug')
                            ->html('<p>Postovani '.$userName.',<br> Vas porudzbina sa popustom iznosi '.$total.' RSD<br>
                            Nedozvoljen minus bi iznosio '.$total.' RSD<br>
                            Maksimalan nedozvoljen minus je 100000.00 RSD
                            Molim vas kontaktirajte admina ukoliko vasa prva porudzbina prelazi ne dozvoljen minus </p>');

                        $this->mailerService->send($message);
                        return 'Pay debt';
                    }
                }
                else{
                    $amount = $debt->getAmount();

                    $currentDate = date_create('now');
                    $currentDateForPdf = date('d.m.Y.');
                    if ($userRabat!=0){
                        $userRabat = $userRabat/100;
                    }

                    $total = 0;
                    foreach ($products as $value){
                        $product = $this->em->getRepository(Product::class)->findOneBy(['id'=>$value->id]);
                        $quantityDatabase = $product->getBalance();
                        $quantityOrder = $value->balance;
                        $quantityOrder = 1;
                        $discountPrice = $product->getDiscountPrice();
                        $regularPrice = $product->getPrice();
                        if ($quantityOrder > $quantityDatabase){
                            // TO DO
                        }else{
                            if ($discountPrice!=0.00){
                                $total = $total + ($discountPrice*$quantityOrder);
                            }
                            else{
                                $total = $total + ($regularPrice*$quantityOrder);
                            }
                        }

                    }
                    $totalWithoutRabat = round($total,2);

                    $total = round( $total - ($total*$userRabat),2);
                    $amountNew = $amount+ $total;

                    if ($amountNew>100000){

                        $message = (new Email())
                            ->to($user->getEmail())
                            ->subject('Dug')
                            ->html('<p>Poštovani '.$userName.',<br> Vaš dug iznosi '.$amount.' RSD, sa vašom porudžbinom bi prešli u nedozvoljen minus.<br>
                            Nedozvoljen minus bi iznosio '.$amountNew.' RSD.<br>
                            Maksimalan nedozvoljen minus je 100000.00 RSD.
                            Molim vas prvo platite prethodne poružbine.</p>');

                        $this->mailerService->send($message);
                        return 'Pay debt';
                    }
                    else{

                    if ($total>0){

                        $order = new Order();

                        $order->setUserId($user);
                        $order->setOrderNote($orderNote);
                        $order->setOrderDate($currentDate);
                        $order->setSent(false);
                        $order->setPrice($total);
                        $order->setPaid(false);
                        $this->em->persist($order);
                        $this->em->flush();
                        $idOrder = $order->getId();

                        $sid = 'AC2a4a1de0e344520c50dd5cc1df681ff4';
                        $token = '52a0dcd7e06e9432ff0a88645f4c24c9';
                        $client = new Client($sid, $token);
                        //$m = new SmsMessage()
                        $client->messages->create(
                        // the number you'd like to send the message to
                            '+381616967616',
                            [
                                // A Twilio phone number you purchased at twilio.com/console
                                'from' => '+14054517789',
                                // the body of the text message you'd like to send
                                'body' => 'Poštovani, vaša porudžbina je evidentirana. Broj porudžbine je '.$idOrder.'.Vaša kožna galenterija. '
                            ]
                        );

                        foreach ($products as $value ){
                            $product = $this->em->getRepository(Product::class)->findOneBy(['id'=>$value->id]);
                            $productName = $product->getName();
                            $quantityDatabase = $product->getBalance();
                            $quantityOrder = $value->balance;
                            $quantityOrder = 1;
                            $discountPrice = $product->getDiscountPrice();
                            $regularPrice = $product->getPrice();
                            if ($quantityOrder > $quantityDatabase){
                                $email1 = (new Email())
                                    ->to($user->getEmail())
                                    ->subject('Proizvod - Kolicina')
                                    ->html('<p>Postovani '.$userName.',<br> '.$productName.' je izbacen iz porudzbine jer trenutna dostupna kolicina je '.$quantityDatabase.',
                                a vi ste hteli da porucite '.$quantityOrder.'. Ukoliko zelite da porucite proizvod napravite novu narudzbu sa dostupnom kolicinom.<br>
                                Kozna galenterija<br>
                                Dalibor<br>
                                Developer & Customer support<br>
                                +385646646464664</p>');
                                $this->mailerService->send($email1);
                            }else{
                                $product->setBalance($quantityDatabase-$quantityOrder);
                                $this->em->persist($product);
                                $this->em->flush();
                                $orderedProduct = new OrderedProducts();
                                $orderedProduct->setOrderNumber($order);
                                $orderedProduct->setProduct($product);
                                $orderedProduct->setNumber($quantityOrder);
                                if ($discountPrice!=0.00){
                                    $orderedProduct->setPrice($discountPrice);
                                }
                                else{
                                    $orderedProduct->setPrice($regularPrice);
                                }
                                $this->em->persist($orderedProduct);
                                $this->em->flush();
                            }

                        }
                        $pdfProducts = $this->em->getRepository(OrderedProducts::class)->findBy(['orderNumber'=>$order]);
                        $pdfArray = [];
                        foreach ($pdfProducts as $pdfProduct){
                            $infos = [];
                            $infos['product'] = $pdfProduct->getProduct()->getName();
                            $infos['category'] = $pdfProduct->getProduct()->getCategory()->getName();
                            $infos['price'] = $pdfProduct->getPrice();
                            $infos['quantity'] = $pdfProduct->getNumber();
                            $pdfArray[]= $infos;
                        }

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
                            'orderDate'=>$currentDateForPdf,
                            'rabat'=>$rabatPdf,
                            'totalWithoutRabat'=>$totalWithoutRabat,
                            'total'=>$total,
                            'products'=>$pdfArray,
                            'note'=>$orderNote,


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


                        $email = (new TemplatedEmail())
                            ->to($user->getEmail())

                            ->subject('Vaša narudžba')
                            ->htmlTemplate('order/email.html.twig')
                            ->context([
                                'name'=>$userName,
                                'idOrder'=>$idOrder,
                                'orderDate'=>$currentDateForPdf,
                                'rabat'=>$rabatPdf,
                                'totalWithoutRabat'=>$totalWithoutRabat,
                                'total'=>$total,
                                'products'=>$pdfArray,
                                'note'=>$orderNote
//                            'name'=>$name,
//                            'emailAddress'=>$to,
//                            'expires'=>$expires
                            ])
                            //->attach(sprintf('your-order-%s.pdf',date('Y-m-d')));
                            ->attachFromPath($pdfFilepath);



                        $this->mailerService->send($email);
                        $debt->setAmount($amountNew);
                        $this->em->persist($debt);
                        $this->em->flush();

                        return 'Success';

                    }}

                }




            }
            else{
                $email = (new Email())
                    ->to($user->getEmail())
                    ->subject('Verifikacija')
                    ->html("
                    <h2>Poštovani  $userName</h2>
                    <h4> Porudžbina</h4>   
                    <p>Ne možete da kreirate porudžbinu jer vas administrator nije još verifikovao. Molim vas sačekajte</p>
                    <br>
                    <h4>Kožna galenterija</h4>
                    <h4>Dalibor</h4>
                    <h4>+38564655456</h4>                
                    ");
                $this->mailerService->send($email);
                return 'Admin did not verify you';
            }

        }
        else{
            $email = (new Email())
                ->to($user->getEmail())
                ->subject('Verifikacija')
                ->html("
                    <h2>Poštovani  $userName</h2>
                    <p>Neuspela porudžbina</p>
                    <p>Ne možete da kreirate porudžbinu jer vaša email adresa nije verifikovana. Molim vas odradite verifikaciju.</p>
                    <br>
                    <h4>Kožna galenterija</h4>
                    <h4>Dalibor</h4>
                    <h4>+38564655456</h4>                
                    ");
            $this->mailerService->send($email);
            return 'Email not verified';

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