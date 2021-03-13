<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Form\OrderFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/commande", name="order")
     */
    public function index(Cart $cart, Request $request): Response
    {
        //getValues permet de récupérer les données puisqu'il y a une relation entre User et Adresse
        // condition : si je n'ai pas d'adresse, je vais en créer une
        //sinon je récupére la commande de mon user et j'affiche ces données dans mon formulaire
        if (!$this->getUser()->getAddresses()->getValues()) {

            return $this->redirectToRoute('add_account_address');
        }

        $form = $this->createForm(OrderFormType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($form->getData());
        }

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFullCart()
        ]);
    }
}
