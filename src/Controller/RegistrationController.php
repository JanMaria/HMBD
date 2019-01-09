<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\RegistrationFormType;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;
use App\FormHandler\FormHandler;

class RegistrationController extends AbstractController
{
  /**
   * @Route("/register", name="user_registration")
   */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        Security $security,
        FormHandler $handler
    ) {
        if ($security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('article_list');
        }

        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($user);
            // $entityManager->flush();
            try {
                $handler->handleForm($form);
            } catch (ORMException $exception) {
                $this->addFlash('dbFailure', 'Błąd obsługi bazy danych');
                goto end;
            }

            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->container->get('security.token_storage')->setToken($token);
            $this->container->get('session')->set('_security_main', serialize($token));

            return $this->redirectToRoute('article_list');
        }
        end:
        return $this->render('user/register.html.twig', ['form' => $form->createView()]);
    }
}
