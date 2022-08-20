<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;

#[AsCommand(
    name: 'app:send:newsletter',
    description: 'Add a short description for your command',
)]
class SendNewsletterCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em,private readonly MailerInterface $mailerService,string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $products = $this->em->getRepository(Product::class)->findBy(array(),array('discountPrice'=>'DESC'),5);
        $topFiveProducts = [];
        $users = $this->em->getRepository(User::class)->findAll();
        //$users = $this->em->getRepository(User::class)->findBy(['isEmailVerified'=>true]);
        $emails = [];
        foreach ($users as $user){
            $emails[] = $user->getEmail();
        }
        foreach ($products as $product){
            $eachProduct = [];
            $productName = $product->getName();
            $productDescription = $product->getDescription();
            $productOldPrice = $product->getPrice();
            $productBalance = $product->getBalance();
            $productNewPrice = $product->getDiscountPrice();

            $category = $this->em->getRepository(Category::class)->findOneBy(['id'=>$product->getCategory()]);
            $categortName = $category->getName();

            if($productNewPrice!=0.00){
                $eachProduct['name'] = $productName;
                $eachProduct['description'] = $productDescription;
                $eachProduct['oldPrice'] = $productOldPrice;
                $eachProduct['newPrice'] = $productNewPrice;
                $eachProduct['balance'] = $productBalance;
                $eachProduct['categoryName'] = $categortName;
                $topFiveProducts[] =$eachProduct;
            }



        }
        if (!empty($topFiveProducts)){

        foreach ($emails as $email){
        $email = (new TemplatedEmail())
            ->to($email)
            ->subject('Izdvajamo top proizvode sa cenom na popustu')
            ->htmlTemplate('newsletter/newsletter.html.twig')
            ->context([
                'products'=>$topFiveProducts
            ]);
        $this->mailerService->send($email);

        }
        }



        $io->success('Newsletter sent!');

        return Command::SUCCESS;
    }
}
