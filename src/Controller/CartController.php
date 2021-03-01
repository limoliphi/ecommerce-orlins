<?php

namespace App\Controller;

use App\Classe\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/mon-panier", name="cart_index")
     */
    public function index(Cart $cart): Response
    {
        dd($cart->getCart());

        return $this->render('cart/index.html.twig');
    }

    /**
     * @Route("/cart/add/{id}", name="add_to_cart")
     */
    public function addCart(Cart $cart, $id): Response
    {
        $cart->addProductToCart($id);

        return $this->redirectToRoute('cart_index');
    }

    /**
     * @Route("/cart/remove", name="remove_cart")
     */
    public function removeCart(Cart $cart): Response
    {
        $cart->removeProductToCart();

        return $this->redirectToRoute('product_index');
    }
}
