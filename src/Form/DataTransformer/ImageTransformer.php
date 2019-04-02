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
// (i może usuwania pliku) -> dużo rzeczy może pójść nie tak na każdym kroku w tym procesie chyba
class ImageTransformer implements DataTransformerInterface
{
    private $requestStack;
    private $entityManager;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }
    public function transform($imageAddress)
    {
        if ($imageAddress === null) {
            dump('transform - imageAddress = null');
            return null;
        }
        dump('transform - imageAddress != null');
        return new File($imageAddress);
    }

    public function reverseTransform($imageFile)
    {
        $articleRepository = $this->entityManager->getRepository(Article::class);
        $articleId = $this->requestStack->getCurrentRequest()->get('id');
        if ($imageFile === null) {#or ""..?
            dump('reveerseTransform - imageFile = null');
            return ($articleId === null) ? null : $articleRepository->find($articleId)->getImage();
        }
        dump('reverseTransform - imageFile != null');
        if ($articleId !== null) {
            $imageAddress = $articleRepository->find($articleId)->getImage();
            if ($imageAddress !== null && $imageAddress !== 'uploads/images/default_image.jpeg') {
                $fileSystem = new Filesystem();
                $fileSystem->remove($imageAddress);
            }
        }
        $directory = 'uploads/images/';
        $imageName = md5(uniqid()).'.'.$imageFile->guessExtension();
        $imageFile->move($directory, $imageName);
        return $directory.$imageName;
    }
}
