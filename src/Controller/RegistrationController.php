<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\RegistrationForm;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;

class RegistrationController extends AbstractController
{
  /**
   * @Route("/register", name="user_registration")
   */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, Security $security)
    {
        if ($security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('article_list');
        }

        $form = $this->createForm(RegistrationForm::class);

        $form->handleRequest($request);

        $user = $form->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->container->get('security.token_storage')->setToken($token);
            $this->container->get('session')->set('_security_main', serialize($token));

            return $this->redirectToRoute('article_list');
        }

        return $this->render('user/register.html.twig', ['form' => $form->createView()]);
    }
}
