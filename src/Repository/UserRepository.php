<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

// metoda, którą chciałem użyć przy funkcjonalności autocomplete przy dodawaniu
// autora do nowego artykułu... co jednak jest chyba znacznie trudniejsze
// bo nie ma gotowego formularza obsługującego taką funkcjonalność
// (czegoś łączącego TextType i ChoiceType)
    public function findByPartialEmail($partialEmail)
    {
      $users = $this->createQueryBuilder('user')
        ->where('user.email LIKE :partialEmail')
        ->setParameter('partialEmail', '%'.$partialEmail.'%')
        ->getQuery()
        ->getResult();

      $assocUsers = array();
      foreach ($users as $user) {
        $assocUsers[$user->getEmail()] = $user;
      }

      return $assocUsers;
    }

}
