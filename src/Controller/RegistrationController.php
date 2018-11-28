<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\RegistrationForm;
use App\Entity\User;

class RegistrationController extends AbstractController
{
  /**
   * @Route("/register", name="user_registration")
   */
  public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
  {
    $user = new User();
    $form = $this->createForm(RegistrationForm::class, $user);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
      $user->setPassword($password);

      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($user);
      $entityManager->flush();

      return $this->redirectToRoute('article_list');
    }

    return $this->render('user/register.html.twig', ['form' => $form->createView()]);
  }
}
