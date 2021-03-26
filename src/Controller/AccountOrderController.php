<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountOrderController extends AbstractController
{
    /**
     * @Route("/compte/mesc-commandes", name="account_order")
     */
    public function index(): Response
    {
        return $this->render('account/index_order.html.twig');
    }
}
