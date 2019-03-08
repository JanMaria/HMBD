<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Entity\Comment;
use App\Entity\Article;
use App\Validator\Constraints\IsCurseFree;
use Doctrine\ORM\ORMException;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment/add", name="new_comment")
     */
    public function addComment(Request $request, Security $security, ValidatorInterface $validator)
    {
        $comment = new Comment();

        $comment
        ->setContent($request->query->get('commentContent'))
        ->setArticle($this->getDoctrine()->getRepository(Article::class)
        ->find($request->query->get('articleId')));
        $user = $security->getUser();
        if (null !== $user) {
            $comment
            ->setName($user->getName().' '.$user->getSurname())
            ->setEmail($user->getEmail());
        } else {
            $comment
            ->setName($request->query->get('commentAuthor'))
            ->setEmail($request->query->get('commentAuthorEmail'));
        }

        $violations = $validator->validate($comment);
        $article = $this->getDoctrine()->getManager()->find(Article::class, $request->query->get('articleId'));

        if ($violations->has(0)) {
            $errors = [];
            for ($i = 0; $i < $violations->count(); $i++) {
                $errors[$violations->get($i)->getPropertyPath()] = $violations->get($i)->getMessage();
            }

            return $this->render('articles/show.html.twig', [
                'article' => $article,
                'errors' => $errors,
            ]);
        }

        try {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($comment);
            $manager->flush();
        } catch (ORMException $exception) {
            $this->addFlash('dbFailure', 'Nie udało się dodać komentarza. Spróbuj ponownie');
            return $this->render('articles/show.html.twig', ['article' => $article]);
        }

        $this->addFlash('success', 'Dodano komentarz');

        return $this->redirectToRoute('article_show', ['id' => $request->query->get('articleId')]);
    }
}
