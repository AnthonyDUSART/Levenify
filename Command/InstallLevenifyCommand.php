<?php

namespace Levenify\LevenifyBundle\Command;

use Levenify\LevenifyBundle\ORM\Doctrine\DQL\Levenshtein;
use Levenify\LevenifyBundle\ORM\Doctrine\DQL\LevenshteinRatio;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallLevenifyCommand extends Command
{
    private $em;
    protected static $defaultName = 'levenify:install';


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Install LEVENSHTEIN function in your database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);
        
        $functions = array(
            Levenshtein::getFunctionName() => Levenshtein::getImportSql(),
            LevenshteinRatio::getFunctionName() => LevenshteinRatio::getImportSql()
        );
        $output->writeln("Installing... :\n");

        foreach($functions as $name => $function) {
            $error = null;

            $output->write(sprintf("Function: %s()... ", $name));
            try
            {
                $this->em->getConnection()->prepare($function)->execute();
            }
            catch(DBALException $e) {
                $error = $e->getMessage();
            }

            
            if(!empty($error))
            {
                $output->write("[fail]\n");
                $io->error($error);
            }
            else
            {
                $output->write("[done]\n");
            }
        }

        $io->success("Finished installation");

        return 0;
    }
}
