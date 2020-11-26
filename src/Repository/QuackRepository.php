<?php

namespace App\Repository;

use App\Entity\Quack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Quack|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quack|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quack[]    findAll()
 * @method Quack[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Quack      findTag($tags)
 */
class QuackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quack::class);
    }

    public function findAllDesc()
    {
        return $this->findBy(array(), array('created_at' => 'DESC'));
    }

    public function search($duckName)
    {
        return $this->createQueryBuilder('q')
            ->leftJoin('q.author', 'author')
            ->leftJoin('q.tags', 'tag')
            ->andWhere('author.duckName LIKE :searchedName OR tag.name LIKE :searchedName')
            ->setParameter('searchedName', '%' . $duckName . '%')
            ->orderBy('q.created_at', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function searchByTag($tagName) {
        return $this->createQueryBuilder('q')
            ->leftJoin('q.tags', 'tag')
            ->andWhere('tag.name LIKE :t')
            ->setParameter('t', '%'.$tagName.'%')
            ->orderBy('q.created_at', 'DESC')
            ->getQuery()
            ->execute();
    }
}
