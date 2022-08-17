<?php

namespace App\Command;

use App\Entity\Logs;
use App\Entity\User;
use App\Repository\LogsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:expired',
    description: 'Deleting created user where time expired',
)]
class UserExpiredCommand extends Command
{
    private $userRepository;
    private $logsRepository;
    public function __construct(private  readonly EntityManagerInterface $em,UserRepository $userRepository,
                                LogsRepository $logsRepository, string $name = null)
    {
        $this->userRepository = $userRepository;
        $this->logsRepository = $logsRepository;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
         //   ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('dry-run')){
            $io->note('Dry mode enabled');
            $count = $this->userRepository->countExpired();
        }else{
            $timeInt = strtotime(date('Y-m-d H:i:s'));
            $users = $this->em->getRepository(User::class)->findBy(['isEmailVerified'=>false]);
            $count = 0;
            foreach ($users as $user){
                if ($user->getVerificationExpire()<$timeInt){
                    $logs = $this->em->getRepository(Logs::class)->findBy(['user'=>$user]);
                    foreach ($logs as $log){
                        $this->logsRepository->remove($log);
                        $this->em->flush();
                    }
                    $count = $this->userRepository->deleteExpired();
                }
            }

        }

//        $arg1 = $input->getArgument('arg1');
//
//        if ($arg1) {
//            $io->note(sprintf('You passed an argument: %s', $arg1));
//        }
//
//        if ($input->getOption('option1')) {
//            // ...
//        }

        $io->success(sprintf('Deleted "%d" expired',$count));

        return Command::SUCCESS;
    }
}
