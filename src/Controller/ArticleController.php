<?php

declare(strict_types=1);

namespace App\Controller;

use App\FormHandler\FormHandler;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Form\Type\ArticleType;
use App\Form\Type\FiltersType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Exception\NotSupported;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_list")
     */
    public function index(Request $request, ArticleRepository $articleRepository): Response
    {
        // $parameters = [
        //     'from_date_filter',
        //     'to_date_filter',
        //     'sort_filter',
        //     'articles_per_page_filter',
        // ];
        // foreach ($parameters as $parameter) {
        //     $options[$parameter] = $request->query->get($parameter);
        // }
        $filters = [];
        $articles = [];

        $form = $this->createForm(FiltersType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
            // throw new \Exception('some exception');
        }

        // TODO: try-catch chyba powinien być wprowadzony jako AOP? Tylko nie wiem jak się
        // to robi jeszcze. I czy się da.
        // Albo zrobić EventListener który słucha KernelEvents::EXCEPTION, sprawdza czy
        // if (instanceof ORMException) {wykonuje zawartość bloku catch} else {rzuca przechwywcony wyjątek dalej}
        try {
            // throw new ORMException('exception');
            // $articles = $this->gatherList();
            $articles = $articleRepository->gatherList($filters);
        } catch (ORMException $exception) {
            $this->addFlash('dbFailure', 'Błąd obsługi bazy danych');
        }

        return $this->render('articles/index.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles,
            // 'options' => $data,
        ]);
    }

    private function gatherList()
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $articleList = [];
        if ($this->isGranted('ROLE_ADMIN')) {
            $articleList = $repo->findAll();
        } else {
            $articleList = $repo->findBy(['isPublished' => true]);
            $articleList = ($this->isGranted('IS_AUTHENTICATED_FULLY')) ?
            \array_merge($articleList, $repo->findBy(['user' => $this->getUser()])) : $articleList;
        }

        return $articleList;
    }

    /**
     * @Route("/search", name="article_search")
     */
    public function search(Request $request): Response
    {
        $query = $request->query->get('query');
        $articles = null;
        try {
            throw new \Exception('sth');
            $articles = $this->getDoctrine()->getRepository(Article::class)->findByPartialTitle($query);
        // } catch (ORMException $exception) {
        } catch (\Exception $exception) {
            $this->addFlash('dbFailure', 'Błąd obsługi bazy danych');
        }

        return $this->render('articles/index.html.twig', ['articles' => $articles]);
    }

    /**
     * @Route("/article/new", name="new_article")
     * @Security("is_granted('ROLE_USER')", statusCode=403)
     */
    public function new(Request $request, FormHandler $handler): Response
    {
        $form = $this->createForm(ArticleType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // throw new \Exception('');
                $handler->handleForm($form);
            // } catch (\Exception $exception) {
            } catch (ORMException $exception) {
                $this->addFlash('dbFailure', 'Błąd obsługi bazy danych');
                goto end;
            }

            $this->addFlash('success', 'Dodano artykuł');

            return $this->redirectToRoute('article_list');
        }
        end:
        return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/article/edit/{id}", name="edit_article")
     * @Security("is_granted('ROLE_ADMIN') or user.hasArticle(id)", statusCode=403)
     */
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article, [
          'isPublishedOptions' => [
            'tak' => true,
            'nie' => false,
          ]
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();
            } catch (ORMException $exception) {
                $this->addFlash('dbFailure', 'Błąd obsługi bazy danych');
                goto end;
            }
            $this->addFlash('success', 'Artykuł został zedytowany');

            return $this->redirectToRoute('article_list');
        }
        end:
        return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/article/{id<\d+>}", name="article_show")
     */
    public function show(Article $article): Response
    {
        return $this->render('articles/show.html.twig', ['article' => $article]);
    }

    /**
     * @Route("/article/delete", name="delete_article")
     * @Security("is_granted('ROLE_ADMIN') or user.hasArticle(request.get('article_id'))", statusCode=403)
     */
    public function delete(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $article = $entityManager->find(Article::class, $request->request->get('article_id'));

        try {
            $entityManager->remove($article);
            $entityManager->flush();
        } catch (ORMException $exception) {
            $this->addFlash('dbFailure', 'Błąd obsługi bazy danych');
        }

        return $this->redirectToRoute('article_list');
    }

    /**
     * @Route("/article/publish/{id}", name="publish_article")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function publish(Article $article): Response
    {
        if (!$article->getIsPublished()) {
            $article->setIsPublished(true);
        }

        try {
            $this->getDoctrine()->getManager()->flush();
        } catch (ORMException $exception) {
            $this->addFlash('dbFailure', 'Błąd obsługi bazy danych');
            goto end;
        }
        $this->addFlash('success', 'Artykuł został opublikowany');

        end:
        return $this->redirectToRoute('article_list');
    }
}
