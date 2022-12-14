<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use DateTime;
use DateTimeZone;
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

        $date = new DateTime('now');
        $date = $date->modify( '-1 day' );
        $products = $this->em->getRepository(Product::class)->findBy(['datum'=>$date]);
        $newProducts = [];

        $users = $this->em->getRepository(User::class)->findAll();

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

            $category = $this->em->getRepository(Category::class)->findOneBy(['id'=>$product->getCategory()]);
            $categortName = $category->getName();

            $eachProduct['name'] = $productName;
            $eachProduct['description'] = $productDescription;
            $eachProduct['oldPrice'] = $productOldPrice;
            $eachProduct['balance'] = $productBalance;
            $eachProduct['categoryName'] = $categortName;
            $eachProduct['image'] = $product->getImage();
            $newProducts[] =$eachProduct;

        }
        if(!empty($newProducts)){
            foreach ($emails as $email){
            $send = (new TemplatedEmail())
                ->to($email)
                ->subject('Proizvodi dodati u poslednjih 24h')
                ->htmlTemplate('newProducts/newProducts.html.twig')
                ->context([
                    'products'=>$newProducts
                ]);
            $this->mailerService->send($send);
            }

        }

        $io->success('Sending emails, with last added product.');

        return Command::SUCCESS;
    }
}
