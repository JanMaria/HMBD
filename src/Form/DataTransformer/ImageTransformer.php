<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Entity\Article;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\EntityManagerInterface;

// TODO: tutaj chyba trzeba jeszcze wychwytywać wyjątki w przypadku niepowodzenia próby zapisania pliku
// (i może usuwania pliku)
class ImageTransformer implements DataTransformerInterface
{
    // private $security;
    private $requestStack;
    private $entityManager;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }
    // TODO: może o tym trzeba decydować w twigu po prostu??
    public function transform($imageAddress)
    {
        // dd("transform method used");
        // dd($imageAddress);
        // $qb = $this->entityManager->createQueryBuilder();
        // dd($qb
        //     ->select('a')
        //     ->from('App\Entity\Article', 'a')
        //     ->where($qb->expr()->eq('a.id', $this->requestStack->getCurrentRequest()->get('id')))
        //     ->getQuery()
        //     ->getSingleResult()
        //     ->getImage());
        if ($imageAddress === null) {# or ""?
            dump('transform - imageAddress = null');
            return null;
            // return new File('uploads/images/default_image.jpeg');
        }
        dump('transform - imageAddress != null');
        return new File($imageAddress);
    }

    public function reverseTransform($imageFile)
    {
        // dd($imageFile);
        $articleRepository = $this->entityManager->getRepository(Article::class);
        $articleId = $this->requestStack->getCurrentRequest()->get('id');
        if ($imageFile === null) {#or ""..?
            dump('reveerseTransform - imageFile = null');
            return ($articleId === null) ? null : $articleRepository->find($articleId)->getImage();
            // return $qb #TODO: tutaj entityManager->getRepository(Article::class)->find($this->requestStack->getCurrentRequest()->get('id'))->getImage();
            //     ->select('a')
            //     ->from('App\Entity\Article', 'a')
            //     ->where($qb->expr()->eq('a.id', $this->requestStack->getCurrentRequest()->get('id')))
            //     ->getQuery()
            //     ->getSingleResult()
            //     ->getImage();

            // dd($qb
            //     ->select('a')
            //     ->from('App\Entity\Article', 'a')
            //     ->where($qb->expr()->eq('a.id', $this->requestStack->getCurrentRequest()->get('id')))
            //     ->getQuery()
            //     ->getSingleResult()
            //     ->getImage());
        }
        dump('reverseTransform - imageFile != null');
        if ($articleId !== null) {
            $imageAddress = $articleRepository->find($articleId)->getImage();
            if ($imageAddress !== null) {
                $fileSystem = new Filesystem();
                $fileSystem->remove($imageAdress);
            }
        }
        $directory = 'uploads/images/';
        $imageName = md5(uniqid()).'.'.$imageFile->guessExtension();
        $imageFile->move($directory, $imageName);
        return $directory.$imageName;
        //TODO: $imageFile-> przenieść do folderu; wcześniej nadać nazwę; zapisać nazwę w encji
        // dd($imageFile->getClientOriginalName());
    }
}
