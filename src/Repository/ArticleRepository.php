<?php

namespace App\Repository;

use App\Entity\Article;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    //public function myFindAllWithPaging($page, $nbPerPage)
    //{
    //    $query = $this->createQueryBuilder('a')
    //      ->andWhere('a.published = 1')
    //      ->orderBy('a.created_at','desc')
    //      ->getQuery()
    //      ->setFirstResult(($page - 1) * $nbPerPage)
    //      ->setMaxResults($nbPerPage);

    //  return new Paginator($query);
    //}

    public function myFindAllWithPaging($page, $nbPerPage)
    {
        $query = $this->createQueryBuilder('a')
            ->andWhere('a.published = 1')
            ->orderBy('a.created_at','desc')
            ->leftJoin('a.comments', 'c')
            ->addOrderBy('c.created_at', 'asc')
            ->addSelect('c')
            ->getQuery()
            ->setFirstResult(($page - 1) * $nbPerPage)
            ->setMaxResults($nbPerPage);

           return new Paginator($query);
    }

    public function findSlugWithCategories($slug)
    {
        return $this->createQueryBuilder('a')
            ->where('a.slug=:slug')->setParameter('slug', $slug)
            ->leftJoin('a.categories', 'c')
            ->addSelect('c')
            ->getQuery() -> getOneOrNullResult();
    }

    public function findWithCategories($id)
    {
        return $this->createQueryBuilder('a')
            ->where('a.id=:id')->setParameter('id', $id)
            ->leftJoin('a.categories', 'c')
            ->addSelect('c')
            ->getQuery() -> getOneOrNullResult();
    }

    public function findByCategory($idCategory)
    {
        $query = $this->createQueryBuilder('a')
            ->select('a')
            ->orderBy('a.created_at','desc')
            ->leftJoin('a.categories', 'c')
            ->Where('c.id=:id')->setParameter('id', $idCategory)
            ->addSelect('c')
            ->getQuery()
            ->getResult();
        return $query;
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
