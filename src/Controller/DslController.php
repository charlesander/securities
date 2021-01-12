<?php

namespace App\Controller;

use App\Entity\Fact;
use Classes\Expression;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DslController extends AbstractController
{
    /**
     * This function takens the expression contained within the POST request body and maps it onto an
     * Expression object. The expression object calculates the value for the expression and returns it.
     * Example request:
     * Content-Type application/json
     * POST /dsl
     * {
     * "expression": {"fn": "*", "a": "sales", "b": 2},
     * "security": "ABC"
     * }
     * @Route("/dsl", name="dsl", methods={"POST"})
     */
    public function index(Request $request): Response
    {
        $factRepository = $this->getDoctrine()->getRepository(Fact::class);
        $expression = new Expression(
            $factRepository,
            $request->get('security'),
            $request->get('expression')
        );

        return $this->json(['message' => 'OK', 'value' => $expression->getValue()]);
    }

}
