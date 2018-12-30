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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_list")
     */
    public function index(): Response
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();

        return $this->render('articles/index.html.twig', ['articles' => $articles]);
    }

    /**
     * @Route("/search", name="article_search")
     */
    public function search(Request $request): Response
    {
        $query = $request->query->get('query');
        $articles = $this->getDoctrine()->getRepository(Article::class)->findByPartialTitle($query);

        return $this->render('articles/index.html.twig', ['articles' => $articles]);
    }

    /**
     * @Route("/article/new", name="new_article")
     * @Security("is_granted('ROLE_USER')", statusCode=403)
     */
    public function new(Request $request, FormHandler $handler): Response
    {
        $form = $this->createForm(EditArticleForm::class);

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

        return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/article/edit/{id}", name="edit_article")
     * @Security("is_granted('ROLE_ADMIN') or user.hasArticle(id)", statusCode=403)
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

    /**
    * @Route("/article/{id<\d+>}", name="article_show")
    */
    public function show(Article $article): Response
    {
        return $this->render('articles/show.html.twig', ['article' => $article]);
    }

    // public function delete(Request $request, $articleId): Response
    // {
    //     $entityManager = $this->getDoctrine()->getManager();
    //     $article = $entityManager->find(Article::class, $articleId);
    //     $entityManager->remove($article);
    //     $entityManager->flush();
    //
    //     return $this->redirectToRoute('article_list');
    // }

    /**
    * @Route("/article/delete", name="delete_article")
    * @Security("is_granted('ROLE_ADMIN') or user.hasArticle(request.get('article_id'))", statusCode=403)
    */
    public function delete(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $article = $entityManager->find(Article::class, $request->request->get('article_id'));

        $entityManager->remove($article);
        $entityManager->flush();

        return $this->redirectToRoute('article_list');
    }
}
