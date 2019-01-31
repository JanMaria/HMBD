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
use Symfony\Component\HttpFoundation\File\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Exception\NotSupported;

class ArticleController extends AbstractController
{
    /**
     * @Route("/{currentPage<\d+>}", defaults={"currentPage"=1}, name="article_list")
     */
    public function index(Request $request, ArticleRepository $articleRepository, $currentPage): Response
    {
        $metadata = [];
        if ($request->getQueryString() !== null) {
            $tempMetadata = explode('&', $request->getQueryString());
            foreach ($tempMetadata as $data) {
                $data = explode('=', $data);
                $metadata[$data[0]] = $data[1];
            }
        }
        dump($metadata);

        $metadata['perPage'] = ($request->query->get('perPage') === null) ? 10 : $request->query->get('perPage');
        $articles = $articleRepository->getSubpage($metadata, $currentPage, $metadata['perPage']);

        return $this->render('articles/index.html.twig', [
            // 'form' => $form->createView(),
            'articles' => $articles,
            // 'subpages' => $subpages,
            'currentPage' => $currentPage,
            // 'perPage' => $perPage,
            'metadata' => $metadata,
        ]);
    }

    // /**
    //  * @Route("/search", name="article_search")
    //  */
    // public function search(Request $request): Response
    // {
    //     $query = $request->query->get('query');
    //     $articles = null;
    //     try {
    //         throw new \Exception('sth');
    //         $articles = $this->getDoctrine()->getRepository(Article::class)->findByPartialTitle($query);
    //     // } catch (ORMException $exception) {
    //     } catch (\Exception $exception) {
    //         $this->addFlash('dbFailure', 'Błąd obsługi bazy danych');
    //     }
    //
    //     return $this->render('articles/index.html.twig', ['articles' => $articles]);
    // }

    /**
     * @Route("/article/new", name="new_article")
     * @Security("is_granted('ROLE_USER')", statusCode=403)
     */
    public function new(Request $request, FormHandler $handler): Response
    {
        $form = $this->createForm(ArticleType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $image = $form->getData()->getImage();
            try {
                // if (!is_string($image)) {
                //     // $imageName = md5(uniqid()).'.'.$image->guessExtension();
                //     // $image->move('uploads/images/', $imageName);
                //     // $form->getData()->setImage('uploads/images/'.$imageName);
                // }
                $handler->handleForm($form);
            } catch (FileException $fException) {
                $this->addFlash('fileFailure', 'Nie udało się dodać zdjęcia');
                return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
            } catch (ORMException $exception) {
                $this->addFlash('dbFailure', 'Nie udało się dodać artykułu');
                return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
            }

            $this->addFlash('success', 'Dodano artykuł');

            return $this->redirectToRoute('article_list');
        }
        return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/article/edit/{id}", name="edit_article")
     * @Security("is_granted('ROLE_ADMIN') or user.hasArticle(id)", statusCode=403)
     */
    public function edit(Request $request, Article $article): Response
    {
        //$article->setImage(new File($article->getImage()));
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // dd($article->getImage());
                // dd($form->get('image'));
                // if ($form->get('image')) {}
                $this->getDoctrine()->getManager()->flush();
            } catch (ORMException $exception) {
                $this->addFlash('dbFailure', 'Nie udało się zedytować artykułu');
                return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
            }
            $this->addFlash('success', 'Artykuł został zedytowany');

            return $this->redirectToRoute('article_list');
        }
        return $this->render(
            'articles/edit.html.twig',
            [
                'form' => $form->createView(),
                'img' => $form->getData()->getImage(),
                // 'img' => ($form->getData()->getImage() === null) ?
                //     'uploads/images/default_image.jpeg' : $form->getData()->getImage(),
            ]
        );
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
     * @Route("/article/publish/{id}", name="de_publish_article")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function changeIsPublished(Article $article): Response
    {
        $article->setIsPublished(!$article->getIsPublished());

        try {
            $this->getDoctrine()->getManager()->flush();
        } catch (ORMException $exception) {
            $this->addFlash('dbFailure', 'Nie udało się zmienić opcji isPublished');
            return $this->redirectToRoute('article_list');
        }
        $this->addFlash('success', 'Opcja isPublished została zmieniona');

        return $this->redirectToRoute('article_list');
    }
}
