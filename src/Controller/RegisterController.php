<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/inscription", name="register")
     */
    public function register(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;

        $user = new User();
        $form = $this->createForm(RegisterFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $search_email = $em->getRepository(User::class)->findOneByEmail($user->getEmail());

            if (!$search_email) {

                $password = $encoder->encodePassword($user, $user->getPassword());

                $user->setPassword($password);

                $em->persist($user);
                $em->flush();

                $mail = new Mail();
                $content = "Bonjour ".$user->getFirstName()."<br/>Bienvenue sur la première boutique du tout fabriqué à Orlins<br><br/>";
                $mail->send($user->getEmail(), $user->getFirstName(), 'Bienvenue sur La Boutique d\'Orlins', $content);

                $notification = "Votre inscription s'est correctement déroulée. Vous pouvez dès à présent vous connecter à votre compte.";

            } else {

                $notification = "L'email que vous avez renseigné existe déjà";
            }
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
