<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/mon-panier", name="cart_index")
     */
    public function index(Cart $cart): Response
    {
        $cartComplete = [];

        foreach ($cart->getCart() as $id => $quantity) {
            $cartComplete[] = [
                'product' => $this->entityManager->getRepository(Product::class)->findOneById($id),
                'quantity' => $quantity
            ];
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cartComplete
        ]);
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
