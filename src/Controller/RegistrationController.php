<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\RegistrationForm;
use App\Entity\User;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
  /**
   * @Route("/register", name="user_registration")
   */
  public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator)
  {
    // $user = new User();
    $form = $this->createForm(RegistrationForm::class);

    $form->handleRequest($request);

    $user = $form->getData();

    if ($form->isSubmitted() && $form->isValid()) {
      $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
      $user->setPassword($password);

      // $user->setRoles(['ROLE_ADMIN']);

      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($user);
      $entityManager->flush();

      return $this->redirectToRoute('article_list');
    }

    return $this->render('user/register.html.twig', ['form' => $form->createView()]);
  }
}