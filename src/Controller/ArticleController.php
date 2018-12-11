<?php

declare(strict_types=1);

namespace App\Controller;

use App\FormHandler\FormHandler;
use App\Entity\Article;
use App\Form\NewArticleForm;
use App\Form\EditArticleForm;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ArticleController extends AbstractController
{
  /**
   * @Route("/", name="article_list")
   * @Method({"GET"})
   */
  public function index(): Response
  {
    $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();

    return $this->render('articles/index.html.twig', ['articles' => $articles]);
  }

  /**
   * @Route("/search", name="article_search")
   * @Method({"GET"})
   */
   public function search(Request $request): Response
   {
     $query = $request->query->get('query');
     $articles = $this->getDoctrine()->getRepository(Article::class)->findByPartialTitle($query);

     return $this->render('articles/index.html.twig', ['articles' => $articles]);
   }

  /**
   * @Route("/article/new", name="new_article")
   * @Method({"GET", "POST"})
   */
  public function new(Request $request, FormHandler $handler): Response
  {
// tego chyba nie powinno być tutaj... zająć się tym (podobnie w edit() )
    $article = new Article();

    $form = $this->createForm(NewArticleForm::class, $article);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $handler->handleForm($form);
      $article = $form->getData();

      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($article);
      $entityManager->flush();

      $this->addFlash('success', 'Dodano artykuł');

      return $this->redirectToRoute('article_list');
    }

    return $this->render('articles/new.html.twig', ['form' => $form->createView()]);
  }

  /**
   * @Route("/article/edit/{id}", name="edit_article")
   * @Method({"GET", "POST"})
   */
  public function edit(Request $request, Article $article, FormHandler $handler): Response
  {
    $form = $this->createForm(EditArticleForm::class, $article, [
      'isPublishedOptions' => [
        'tak' => true,
        'nie' => false
      ]
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $handler->handleForm($form);

      $this->addFlash('success', 'Artykuł został zedytowany');

      return $this->redirectToRoute('article_list');
    }

    return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
  }

  // tutaj stara metoda bez użycia ParamConverter'a
  // /**
  // * @Route("/article/{id<\d+>}", name="article_show")
  // * @Method({"GET"})
  // */
  // public function show($id)
  // {
  //   $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
  //
  //   return $this->render('articles/show.html.twig', ['article' => $article]);
  // }

  // tutaj, o ile dobrze rozumiem, niejawnie używam ParamConverter'a
  // /**
  // * @Route("/article/{id<\d+>}", name="article_show")
  // * @Method({"GET"})
  // */
  // public function show(Article $article)
  // {
  //   return $this->render('articles/show.html.twig', ['article' => $article]);
  // }

  // a tutaj z jawnym użyciem ParamConverter'a
  /**
  * @Route("/article/{articleID<\d+>}", name="article_show")
  * @Method({"GET"})
  * @ParamConverter("article", options={"mapping"={"articleID"="id"}})
  */
  public function show(Article $article): Response
  {
    return $this->render('articles/show.html.twig', ['article' => $article]);
  }

  /**
  * @Route("/article/delete/{id}", name="delete_article")
  * @Method({"DELETE"})
  */
  public function delete(Request $request, Article $article): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');

    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->remove($article);
    $entityManager->flush();

    return $this->redirectToRoute('article_list');
  }

}
