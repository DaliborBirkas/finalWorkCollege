<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
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
    name: 'app:last:added:product',
    description: 'Add a short description for your command',
)]
class LastAddedProductCommand extends Command
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

        $dayBefore = date('Y-m-d',strtotime("-1 days"));
        $products = $this->em->getRepository(Product::class)->findBy(['dateForPublic'=>$dayBefore]);
        $newProducts = [];
        foreach ($products as $product){
            $eachProduct = [];
            $productName = $product->getName();
            $productDescription = $product->getDescription();
            $productOldPrice = $product->getPrice();
            $productBalance = $product->getBalance();
            $productNewPrice = $product->getDiscountPrice();

            $category = $this->em->getRepository(Category::class)->findOneBy(['id'=>$product->getCategory()]);
            $categortName = $category->getName();

            $eachProduct['name'] = $productName;
            $eachProduct['description'] = $productDescription;
            $eachProduct['oldPrice'] = $productOldPrice;
           // $eachProduct['newPrice'] = $productNewPrice;
            $eachProduct['balance'] = $productBalance;
            $eachProduct['categoryName'] = $categortName;
            $newProducts[] =$eachProduct;

        }
        if(!empty($newProducts)){
            $email = (new TemplatedEmail())
                ->to('dbirkas3@gmail.com')
                ->subject('Proizvodi dodati u poslednjih 24h')
                ->htmlTemplate('newProducts/newProducts.html.twig')
                ->context([
                    'products'=>$newProducts
                ]);
            $this->mailerService->send($email);
        }

        $io->success('Sending emails, with last added product.');

        return Command::SUCCESS;
    }
}
