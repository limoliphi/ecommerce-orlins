<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressFormType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }

    /**
     * @Route("/compte/adresses", name="account_address")
     */
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }

    /**
     * @Route("/compte/ajouter-une-adresse", name="add_account_address")
     */
    public function addAddress(Cart $cart, Request $request): Response
    {
        $address = new Address();

        $form = $this->createForm(AddressFormType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());

            $this->entityManager->persist($address);
            $this->entityManager->flush();

            if ($cart->getCart()) {
                return $this->redirectToRoute('order');
            } else {

                return $this->redirectToRoute('account_address');
            }

        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/ajouter-une-adresse{id}", name="edit_account_address")
     */
    public function editAddress(Request $request, $id, AddressRepository $addressRepository): Response
    {
        $address = $addressRepository->findOneById($id);

        if (!$address || $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('account_address');
        }

        $form = $this->createForm(AddressFormType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();

            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/supprimer-une-adresse{id}", name="delete_account_address")
     */
    public function deleteAddress($id, AddressRepository $addressRepository): Response
    {
        $address = $addressRepository->findOneById($id);

        if ($address && $address->getUser() == $this->getUser()) {

            $this->entityManager->remove($address);

            $this->entityManager->flush();
        }

        return $this->redirectToRoute('account_address');

    }
}
