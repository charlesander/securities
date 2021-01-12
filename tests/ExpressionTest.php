<?php

namespace App\Tests;

use App\Entity\Fact;
use Classes\Expression;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ExpressionTest extends SetupTest
{
    /**
     * @test
     */
    public function testgetArgumentValue1()
    {
        $factRepository = $this->entityManager->getRepository(Fact::class);

        $fn = "*";
        $a = "10";
        $b = "2";

        $requestParams = [
            "expression" => ["fn" => $fn, "a" => $a, "b" => $b],
            "security" => "ABC"
        ];

        $expression = new Expression(
            $factRepository,
            $requestParams['security'],
            $requestParams['expression']
        );


        $this->assertEquals($expression->getArgumentValue($expression->getA()), $a);
        $this->assertEquals($expression->getArgumentValue($expression->getB()), $b);
    }

    /**
     * @test
     */
    public function testgetArgumentValue2()
    {
        $factRepository = $this->entityManager->getRepository(Fact::class);

        $fn1 = "*";
        $a1 = "10";

        $fn2 = "+";
        $a2 = "2";
        $b2 = "2";

        $requestParams = [
            "expression" => ["fn" => $fn1, "a" => $a1, "b" => ["fn" => $fn2, "a" => $a2, "b" => $b2]],
            "security" => "ABC"
        ];

        $expression = new Expression(
            $factRepository,
            $requestParams['security'],
            $requestParams['expression']
        );


        $this->assertEquals($expression->getArgumentValue($expression->getA()), $a1);
        $this->assertEquals($expression->getArgumentValue($expression->getB()), $a2 + $b2);
    }

    /**
     * @test
     */
    public function testgetArgumentValue3()
    {
        $factRepository = $this->entityManager->getRepository(Fact::class);

        $fn = "*";
        $a = "price";
        $b = "2";

        $requestParams = [
            "expression" => ["fn" => $fn, "a" => $a, "b" => $b],
            "security" => "ABC"
        ];

        $expression = new Expression(
            $factRepository,
            $requestParams['security'],
            $requestParams['expression']
        );


        $this->assertEquals($expression->getArgumentValue($expression->getA()), 1);// ABC price = 1
        $this->assertEquals($expression->getArgumentValue($expression->getB()), $b);
    }

    /**
     * @test
     */
    public function testgetArgumentValue4()
    {
        $factRepository = $this->entityManager->getRepository(Fact::class);

        $fn = "*";
        $a = "4";
        $b = "sales";

        $requestParams = [
            "expression" => ["fn" => $fn, "a" => $a, "b" => $b],
            "security" => "CDE"
        ];

        $expression = new Expression(
            $factRepository,
            $requestParams['security'],
            $requestParams['expression']
        );


        $this->assertEquals($expression->getArgumentValue($expression->getA()), $a);
        $this->assertEquals($expression->getArgumentValue($expression->getB()), 12);// CDE sales = 12
    }

    /**
     * @test
     */
    public function calcArgumentValue1()
    {
        $factRepository = $this->entityManager->getRepository(Fact::class);

        $fn = "+";
        $a = "4";
        $b = "sales";

        $requestParams = [
            "expression" => ["fn" => $fn, "a" => $a, "b" => $b],
            "security" => "CDE"
        ];

        $expression = new Expression(
            $factRepository,
            $requestParams['security'],
            $requestParams['expression']
        );

        $value = $expression->calcArgumentValue($requestParams['security'], $a);
        $this->assertEquals((float)$a, $value);

        $value = $expression->calcArgumentValue($requestParams['security'], $b);
        $this->assertEquals($value, 12);// CDE sales = 12

    }

    /**
     * @test
     */
    public function validateExpressionArgument1()
    {
        $factRepository = $this->entityManager->getRepository(Fact::class);

        $fn = "+";
        $a = "7";
        $b = "ebitda";

        $requestParams = [
            "expression" => ["fn" => $fn, "a" => $a, "b" => $b],
            "security" => "CDE"
        ];

        $expression = new Expression(
            $factRepository,
            $requestParams['security'],
            $requestParams['expression']
        );

        $this->assertTrue($expression->validateExpressionArgument('true'));
        $this->assertTrue($expression->validateExpressionArgument(1));
        $this->assertTrue($expression->validateExpressionArgument((double)1));
        $this->assertTrue($expression->validateExpressionArgument([]));

        $this->expectException(\Exception::class);
        $this->assertTrue($expression->validateExpressionArgument((object)[]));
    }

    /**
     * @test
     */
    public function validateExpressionFunctiontest1()
    {
        $factRepository = $this->entityManager->getRepository(Fact::class);

        $fn = "+";
        $a = "4";
        $b = "ebitda";

        $requestParams = [
            "expression" => ["fn" => $fn, "a" => $a, "b" => $b],
            "security" => "CDE"
        ];

        $expression = new Expression(
            $factRepository,
            $requestParams['security'],
            $requestParams['expression']
        );

        $this->assertTrue($expression->validateExpressionFunction('+'));
        $this->assertTrue($expression->validateExpressionFunction('-'));
        $this->assertTrue($expression->validateExpressionFunction('*'));
        $this->assertTrue($expression->validateExpressionFunction('/'));

        $this->expectException(\Exception::class);
        $expression->validateExpressionFunction('h');
    }

    /**
     * @test
     */
    public function validateExpressionValuestest1()
    {
        $factRepository = $this->entityManager->getRepository(Fact::class);

        $fn = "+";
        $a = "4";
        $b = "ebitda";

        $requestParams = [
            "expression" => ["fn" => $fn, "a" => $a, "b" => $b],
            "security" => "CDE"
        ];

        $expression = new Expression(
            $factRepository,
            $requestParams['security'],
            $requestParams['expression']
        );
        $this->assertTrue($expression->validateExpressionValues($requestParams["expression"]));

        $fn = "d"; //incorrect value
        $a = "4";
        $b = "ebitda";

        $requestParams = [
            "expression" => ["fn" => $fn, "a" => $a, "b" => $b],
            "security" => "CDE"
        ];

        $this->expectException(\Exception::class);
        $expression->validateExpressionValues($requestParams["expression"]);
    }
}
