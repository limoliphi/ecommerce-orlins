<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderCancelController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande/erreur/{stripeSessionId}", name="order_cancel")
     */
    public function index($stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        //on vérifie l'utilisateur
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        //envoyer un email au client pour lui indiquer l'échec du paiement de sa commande
        $mail = new Mail();
        $content = "Bonjour " . $order->getUser()->getFirstName() . "<br/>Désolé, votre commande sur la première boutique du tout fabriqué à Orlins a échoué<br><br/>";
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(), 'Votre commande La Boutique d\'Orlins n\'est pas validée', $content);

        return $this->render('order_cancel/index.html.twig', [
            'order' => $order
        ]);
    }
}
