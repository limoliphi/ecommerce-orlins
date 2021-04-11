<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/mot-de-passe-oublie", name="reset_password")
     */
    public function index(Request $request): Response
    {
        //redirige l'utilisateur qui est déjà connecté
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        //on veille à ce que l'adresse email remplie par l'utilisateur soit captée
        //vérifier si l'email existe en base, donc besoin EntityManager
        if ($request->get('email')) {
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));

            //si l'utilisateur est reconnu en base, on lui demande d'initialiser le nouveau mode de passe
            if ($user) {
                //enregistrer en base la demande de reset_password avec user token createdAt
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \DateTime());
                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();

                //envoyer un mail à l'utilisateur avec un lien de mise à jour du mot de passe

                $url = $this->generateUrl('update_password', [
                    'token' => $reset_password->getToken()
                ]);

                $content = "Bonjour ".$user->getFirstName()."<br/>Vous avez demandé à réinitialiser votre mot de passe sur La Boutique d\'Orlins<br/><br/>";
                $content.= "Merci de bien vouloir cliquer sur le lien suivant pour <a href='.$url.'>mettre à jour votre mot de passe</a>";

                $mail = new Mail();
                $mail->send($user->getEmail(), $user->getFirstName().' '.$user->getLastName(), 'Réinitialiser votre mot de passe sur La Boutique d\'Orlins', $content);

                $this->addFlash('notice', 'Vous allez recevoir un email dans quelques minutes vous indiquant la procédure pour réinitialiser votre mot de passe.');
            } else {
                $this->addFlash('notice', 'Cette adresse email est inconnue');
            }

        }

        return $this->render('reset_password/index.html.twig');
    }

    /**
     * @Route("/modifier-mon-mot-de-passe{token}", name="update_password")
     */
    public function updatePassword(Request $request, $token, UserPasswordEncoderInterface $encoder): Response
    {
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);

        if (!$reset_password) {
            return $this->redirectToRoute('reset_password');
        }

        //Vérifier que le createdAt est now - 3 heures
        $now = new \DateTime();
        //On commence par l'erreur
        if ($now > $reset_password->getCreatedAt()->modify('+ 1 hour')) {
            $this->addFlash('notice', 'Votre demande de mot de passe a expiré, merci de la renouveler');
            return $this->redirectToRoute('reset_password');
        }
        //rendre la vue avec le mot de passe et confirmer le mot de passe
        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $new_pwd = $form->get('new_password')->getData();
            //encoder le mdp
            $password = $encoder->encodePassword($reset_password->getUser(), $new_pwd);
            $reset_password->getUser()->setPassword($password);
            //flush des données
            $this->entityManager->flush();
            //notification
            $this->addFlash('notice', 'Votre mot de passe a bien été mis à jour');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_update/update.html.twig', [
            'form' => $form->createView()
        ]);
    }
}