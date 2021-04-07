<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_success")
     */
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        //on vérifie l'utilisateur
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        //modifier le statut isPaid de la commande
        if ($order->getState() == 0) {
            //on vide le panier de l'utilisateur
            $cart = $cart->removeProductsToCart();
            $order->setState(1);
            $this->entityManager->flush();

            //envoyer un email au client pour lui confirmer le paiement de sa commande
            $mail = new Mail();
            $content = "Bonjour " . $order->getUser()->getFirstName() . "<br/>Merci pour votre commande sur la première boutique du tout fabriqué à Orlins<br><br/>";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(), 'Votre commande La Boutique d\'Orlins est bien validée', $content);
        }

        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
