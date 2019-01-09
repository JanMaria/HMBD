<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    private $security;

    public function __construct(RegistryInterface $registry, Security $security)
    {
        parent::__construct($registry, Article::class);
        $this->security = $security;
    }

    public function findByPartialTitle($partialTitle)
    {
        return $this->createQueryBuilder('a')
        ->where('a.title LIKE :partialTitle')
        ->setParameter('partialTitle', '%'.$partialTitle.'%')
        ->getQuery()
        ->getResult();
    }

    public function gatherList(array $filters)
    {
        $qb = $this->createQueryBuilder('a');

        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            $qb->andWhere('a.isPublished = 1');
        } elseif (!$this->security->isGranted('ROLE_ADMIN')) {
            $qb->andWhere('a.user = :thisUser OR a.isPublished = 1')
            ->setParameter('thisUser', $this->security->getUser());
        }


        if (\array_key_exists('from_date_filter', $filters) && $filters['from_date_filter'] !== null) {
            $qb
            ->andWhere($qb->expr()->gte('a.createdAt', $filters['from_date_filter']->format('Ymd')));
                // ->andWhere('a.createdAt >= :from_date_filter')
                // ->setParameter('from_date_filter', $filters['from_date_filter']);
        }

        if (\array_key_exists('to_date_filter', $filters) && $filters['to_date_filter'] !== null) {
            $qb
            ->andWhere($qb->expr()->lt('a.createdAt', $filters['to_date_filter']->format('Ymd')));
                // ->andWhere('a.createdAt >= :from_date_filter')
                // ->setParameter('from_date_filter', $filters['from_date_filter']);
        }

        if (\array_key_exists('sort_filter', $filters) && $filters['sort_filter'] !== null) {
            $sortMethod = \explode('-', $filters['sort_filter']);
            $qb->orderBy($sortMethod[0], $sortMethod[1]);
        }

        if (\array_key_exists('articles_per_page_filter', $filters) && $filters['articles_per_page_filter'] !== null) {
            $qb->setMaxResults($filters['articles_per_page_filter']);
        } else {
            // TODO: wiem, że to jest super brzydkie rozwiązanie. Na razie nie wiem jak to inaczej zaimplementować
            // Myślę, że to nie będzie w formularzu, tylko będą dane w kontrolerze pobierane z requesta
            // a cała paginacja będzie napisany w html'u
            $qb->setMaxResults(10);
        }

        // dump($qb->getQuery()->getSQL());

        return $qb->getQuery()->getResult();
    }

    // private function prepareCriteria(array $filters)
    // {
    //     $criteria = [];
    //     $criteria['user'] => ($serurity->isGranted('ROLE_ADMIN')) ? 'ANY' : $security->getUser();
    //     if ($serurity->isGranted('ROLE_ADMIN')) {
    //         $
    //     } else {
    //         $criteria['user'] => $security->getUser();
    //     }
    //
    //     return $criteria;
    // }
}
