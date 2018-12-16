<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use \Swift_Message;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, \Swift_Mailer $mailer): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // if ($error !== null &&
        //   $this->getDoctrine()->getManager()->getRepository(User::class)->
        //   findOneBy(['email' => $lastUsername]) !== null) {
        //     $message = (new Swift_Message())
        //       ->setSubject('[ytcrud]: Nieudana próba logowania')
        //       ->setFrom(['ytcrud@gmail.com' => 'ytcrud'])
        //       ->setTo($lastUsername)
        //       ->setBody('Ktoś próbował się zalogować na twoje konto przy użyciu niepoprawnego hasła.');
        //
        //       $mailer->send($message);
        //
        //       $this->addFlash('loginFailure', 'Niepoprawne hasło. Wysłano powiadomienie.');
        // }

        return $this->render('security/login.html.twig', [
          'last_username' => $lastUsername,
          'error' => $error
        ]);
    }
}
