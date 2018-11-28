<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }

    // Wykomentowany kod poniżej ma chyba takie samo działanie jak ten nie-wykomentowany
    public function findByPartialTitle($partialTitle)
    {
      // return $this->getEntityManager()
      //   ->createQuery("SELECT a FROM App\Entity\Article a WHERE a.title LIKE '%$partialTitle%'")
      //   ->getResult();
      return $this->createQueryBuilder('a')
        ->where('a.title LIKE :partialTitle')
        ->setParameter('partialTitle', '%'.$partialTitle.'%')
        ->getQuery()
        ->getResult();
    }

}
