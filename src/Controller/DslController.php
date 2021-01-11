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
