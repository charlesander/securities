<?php

namespace App\Tests;

use App\Entity\Fact;
use Classes\Expression;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class SetupTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /** @test */
    public function testTrue(){

        $this->assertTrue(true);
    }
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $application = new Application($kernel);
        $application->setAutoExit(false);

        // Clear database ready for fresh data
        $input = new ArrayInput(array(
            'command' => 'doctrine:database:drop',
            '--force' => '--force'
        ));

        $this->runCommand($application, $input);

        // Create database ready for fresh data
        $input = new ArrayInput(array(
            'command' => 'doctrine:database:create'
        ));

        $output = $this->runCommand($application, $input);

        // setup database schema
        $input = new ArrayInput(array(
            'command' => 'doctrine:migrations:migrate',
            '-n' => '-n'
        ));

        $output = $this->runCommand($application, $input);
        // setup database with test contend
        $input = new ArrayInput(array(
            'command' => 'import:csv',
            'file-names' => 'data/attributes.csv,data/securities.csv,data/facts.csv'
        ));

        $output = $this->runCommand($application, $input);

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @param Application $application
     * @param ArrayInput $input
     * @return string
     * @throws \Exception
     */
    protected function runCommand(Application $application, ArrayInput $input): string
    {
        $output = new BufferedOutput();

        $application->run($input, $output);
        return $output->fetch();
    }
}
