<?php

namespace Classes;

use App\Repository\FactRepository;
use Exception;

class Expression
{
    const VALID_OPERATORS = ['+', '-', '*', '/'];

    /**
     * @var FactRepository
     */
    protected $factRepository;
    /**
     * @var string
     */
    protected string $security;

    /**
     * @var mixed
     */
    protected $fn;

    /**
     * @var mixed
     */
    protected $a;

    /**
     * @var mixed
     */
    protected $b;

    /**
     * Expression constructor.
     * @param FactRepository $factRepository
     * @param string $security
     * @param array $expression
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function __construct(FactRepository &$factRepository, string $security, array $expression)
    {
        $this->validateExpressionValues($expression);
        $this->factRepository = $factRepository;
        $this->security = $security;

        $this->fn = $expression['fn'];
        $this->a = $this->calcArgumentValue($security, $expression['a']);
        $this->b = $this->calcArgumentValue($security, $expression['b']);
    }

    /**
     * @param array $expression
     * @return bool
     * @throws Exception
     */
    public function validateExpressionValues(array $expression)
    {
        return $this->validateExpressionFunction($expression['fn'])
            && $this->validateExpressionArgument($expression['a'])
            && $this->validateExpressionArgument($expression['b']);
    }

    /**
     * @param $expressionFunction
     * @return bool
     * @throws Exception
     */
    public function validateExpressionFunction($expressionFunction)
    {
        if (!in_array($expressionFunction, self::VALID_OPERATORS)) {
            throw new Exception('Invalid fn provided');
        }
        return true;
    }

    /**
     * @param $expressionArgument
     * @return bool
     * @throws Exception
     */
    public function validateExpressionArgument($expressionArgument)
    {
        switch (gettype($expressionArgument)) {
            case 'string':
            case 'array':
            case 'integer':
            case 'double':
                return true;
            default:
                throw new Exception('Invalid expression supplied');
        }
    }

    /**
     * @param string $security
     * @param $argument
     * @return Expression|float|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function calcArgumentValue(string $security, $argument)
    {
        if (is_array($argument)) {
            return new Expression($this->factRepository, $security, $argument);
        } else {
            if (is_numeric($argument)) {
                return (float)$argument;
            } elseif (is_string($argument)) {
                return $this->factRepository->findBySecurityAndAttribute($security, $argument);
            } else {
                return $argument;
            }
        }
    }

    /**
     * @return Expression|float|mixed
     */
    public function getValue()
    {
        $a = $this->getArgumentValue($this->a);
        $b = $this->getArgumentValue($this->b);

        //This can be refactored
        if ($this->fn === '+') {
            return $a + $b;
        }
        if ($this->fn === '-') {
            return $a - $b;
        }
        if ($this->fn === '*') {
            return $a * $b;
        }
        if ($this->fn === '/') {
            return $a / $b;
        }
    }

    /**
     * Assume that if it's an object, it's an Expression. Make sure data is clean.
     * @param $argument
     * @return mixed
     */
    public function getArgumentValue($argument)
    {
        return is_object($argument) ? $argument->getValue() : $argument;
    }

    /**
     * @return mixed
     */
    public function getFn()
    {
        return $this->fn;
    }

    /**
     * @return mixed
     */
    public function getA()
    {
        return $this->a;
    }

    /**
     * @return mixed
     */
    public function getB()
    {
        return $this->b;
    }


}
