<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Posts;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Posts>
 *
 * @method Posts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Posts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Posts[]    findAll()
 * @method Posts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Posts::class);
    }

    private function findAllQuery(
        bool $withAuthors = false,
        string $orderBy = 'created'
    ): QueryBuilder {
        $query = $this->createQueryBuilder('p');

        if ($withAuthors) {
            $query->leftJoin('p.author', 'a')
                ->addSelect('a');
        }

        return $query->orderBy('p.' . $orderBy, 'DESC');
    }

    public function findAllByAuthor(
        int | User $author
    ): array {
        return $this->findAllQuery(
            withAuthors: false,
        )->where('p.author = :author')
            ->setParameter(
                'author',
                $author instanceof User ? $author->getId() : $author
            )->getQuery()->getResult();
    }

//    /**
//     * @return Posts[] Returns an array of Posts objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Posts
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
