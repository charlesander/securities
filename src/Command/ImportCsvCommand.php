<?php

namespace App\Command;

use App\Entity\Attribute;
use App\Entity\Fact;
use App\Entity\Security;
use Ddeboer\DataImport\Reader\CsvReader;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportCsvCommand extends Command
{
    const fileNameArgs = 'file-names';

    protected static $defaultName = 'import:csv';

    private $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->setDescription('Import the provided csv files into the database')
            ->addArgument(
                self::fileNameArgs,
                InputArgument::REQUIRED | InputArgument::REQUIRED,
                'Please provide a comma sepearted listed of file names?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityManager = $this->container->get('doctrine')->getManager();

        $fileNames = $this->gatherFileNames($input);

        foreach ($fileNames as $fileName) {
            $file = new \SplFileObject($fileName);
            $reader = new CsvReader($file);
            $reader->setHeaderRowNumber(0);

            $this->create($file, $reader, $entityManager);
        }

        $io = new SymfonyStyle($input, $output);
        $io->success('You have imported the required data');

        return Command::SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @return array
     * @throws Exception
     */
    protected function gatherFileNames(
        InputInterface $input
    ): array {
        $fileNames = explode(',', $input->getArgument(self::fileNameArgs));

        foreach ($fileNames as $fileName) {
            if (!is_file($fileName)) {
                throw new Exception(sprintf('File does not exist: %s', $fileName));
            }
        }

        return $fileNames;
    }

    /**
     * @param \SplFileObject $file
     * @param CsvReader $reader
     * @param $entityManager
     */
    public function create(\SplFileObject $file, CsvReader $reader, $entityManager): void
    {
        if ($file->getPathname() == 'data/attributes.csv') {
            foreach ($reader as $read) {
                $attribue = new Attribute();
                $attribue->setId($read['id']);
                $attribue->setName($read['name']);
                $entityManager->persist($attribue);
            }
            $entityManager->flush();
        } else {
            if ($file->getPathname() == 'data/securities.csv') {
                foreach ($reader as $read) {
                    $security = new Security();
                    $security->setId($read['id']);
                    $security->setSymbol($read['symbol']);
                    $entityManager->persist($security);
                }
                $entityManager->flush();
            } else {
                if ($file->getPathname() == 'data/facts.csv') {
                    foreach ($reader as $read) {
                        $fact = new Fact();
                        $fact->setSecurity(
                            $entityManager->
                            getRepository(Security::class)->
                            find($read['security_id'])
                        );
                        $fact->setAttribute(
                            $entityManager->
                            getRepository(Attribute::class)->
                            find($read['attribute_id'])
                        );
                        $fact->setValue($read['value']);
                        $entityManager->persist($fact);
                    }
                    $entityManager->flush();
                }
            }
        }
    }
}
