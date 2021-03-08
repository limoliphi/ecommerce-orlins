<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private $session;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;

    }

    public function addProductToCart($id)
    {
        $cart = $this->session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $this->session->set('cart', $cart);

    }

    public function getCart()
    {
        return $this->session->get('cart');
    }

    public function removeProductsToCart()
    {
        return $this->session->remove('cart');

    }

    public function deleteOneProductToCart($id)
    {
        $cart = $this->session->get('cart', []);

        unset($cart[$id]);

        return $this->session->set('cart', $cart);

    }

    public function decreaseCart($id)
    {
        $cart = $this->session->get('cart', []);

        if ($cart[$id] > 1) {
            $cart[$id]--;
        } else {
            unset($cart[$id]);
        }

        return $this->session->set('cart', $cart);

    }

    public function getFullCart()
    {
        $cartComplete = [];

        if ($this->getCart()) {

            foreach ($this->getCart() as $id => $quantity) {
                $myProduct = $this->entityManager->getRepository(Product::class)->findOneById($id);

                if (!$myProduct) {
                    $this->deleteOneProductToCart($id);
                    continue;
                }
                $cartComplete[] = [
                    'product' => $myProduct,
                    'quantity' => $quantity
                ];
            }
        }

        return $cartComplete;
    }

}