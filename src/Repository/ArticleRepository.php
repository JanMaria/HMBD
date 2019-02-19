<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Zend\EventManager\Exception\InvalidArgumentException;

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

    public function getSubpage(array $filters): Paginator
    {
        $query = $this->buildQuery($filters);
        $paginator = $this->paginate($query, $filters['currentPage'], $filters['perPage']);

        return $paginator;
    }
    //
    // public function getSubpage(array $filters, $currentPage, $perPage): Paginator
    // {
    //     $currentPage = ($currentPage === null) ? 1: $currentPage;
    //     $perPage = ($perPage === null) ? 1: $perPage;
    //     $query = $this->buildQuery($filters);
    //     $paginator = $this->paginate($query, $currentPage, $perPage);
    //
    //     return $paginator;
    // }

    private function buildSecurityPart(QueryBuilder $qb)
    {
        if (!$this->security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $qb->andWhere('a.isPublished = 1');
        } elseif (!$this->security->isGranted('ROLE_ADMIN')) {
            $qb
                ->andWhere('a.user = :thisUser OR a.isPublished = 1')
                ->setParameter('thisUser', $this->security->getUser());
        }
    }

    private function buildDateRangePart(array $filters, QueryBuilder $qb)
    {
        if (array_key_exists('dateFrom', $filters) && $filters['dateFrom'] !== "") {
            $qb
            ->andWhere($qb->expr()->gte('a.createdAt', str_replace("-", "", $filters['dateFrom'])));
        }

        if (array_key_exists('dateTo', $filters) && $filters['dateTo'] !== "") {
            $qb
            ->andWhere($qb->expr()->lt('a.createdAt', str_replace("-", "", $filters['dateTo'])));
        }
    }

    private function buildTitleSearchPart(array $filters, QueryBuilder $qb)
    {
        if (array_key_exists('searchQuery', $filters) && $filters['searchQuery'] !== "") {
            $qb
                ->andWhere('a.title LIKE :partialTitle')
                ->setParameter('partialTitle', '%'.$filters['searchQuery'].'%');
        }
    }

    private function buildQuery(array $filters)
    {
        $qb = $this->createQueryBuilder('a');

        $this->buildSecurityPart($qb);
        $this->buildDateRangePart($filters, $qb);
        $this->buildTitleSearchPart($filters, $qb);

        if ($filters['sort'] !== "") {
        // if (array_key_exists('sort', $filters) && $filters['sort'] !== null) {
            $sortMethod = \explode('-', $filters['sort']);
            switch ($sortMethod[0]) {
                case 'title':
                case 'createdAt':
                    $qb->orderBy('a.'.$sortMethod[0], $sortMethod[1]);
                    break;
                default:
                    throw new InvalidArgumentException();
            }
        }

        return $qb->getQuery();
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
