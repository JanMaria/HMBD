<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

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

    private function buildSecurityPart(QueryBuilder $qb): QueryBuilder
    {
        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            $qb->andWhere('a.isPublished = 1');
        } elseif (!$this->security->isGranted('ROLE_ADMIN')) {
            $qb
                ->andWhere('a.user = :thisUser OR a.isPublished = 1')
                ->setParameter('thisUser', $this->security->getUser());
        }

        return $qb;
    }

    // private function buildSearchPart(array $filters, QueryBuilder $qb): QueryBuilder
    // {
    //     $qb
    //         ->andWhere('a.title LIKE :partialTitle')
    //         ->setParameter('partialTitle', '%'.$filters['partialTitle'].'%');
    //
    //     return $qb;
    // }



    // public function findByPartialTitle($partialTitle)
    // {
    //     return $this->createQueryBuilder('a')
    //     ->where('a.title LIKE :partialTitle')
    //     ->setParameter('partialTitle', '%'.$partialTitle.'%')
    //     ->getQuery()
    //     ->getResult();
    // }

    private function buildQuery(array $filters)
    {
        $qb = $this->createQueryBuilder('a');

        $this->buildSecurityPart($qb);

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

        // dump($qb->getQuery()->getSQL());

        return $qb->getQuery();
    }

    public function getSubpage(array $filters, $currentPage, $perPage): Paginator
    {
        $query = $this->buildQuery($filters);
        $paginator = $this->paginate($query, $currentPage, $perPage);

        return $paginator;
    }

    private function paginate($dql, $currentPage, $perPage): Paginator
    {
        $paginator = new Paginator($dql);
        $paginator
            ->getQuery()
            ->setFirstResult($perPage * ($currentPage - 1))
            ->setMaxResults($perPage);

        return $paginator;
    }
}
