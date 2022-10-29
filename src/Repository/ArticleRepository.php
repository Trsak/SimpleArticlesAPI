<?php

namespace App\Repository;

use App\Entity\Article;
use App\Repository\Exception\ArticleNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findAllByCriteria(int $page = 1, int $perPage = 5): array
    {
        $query = $this->createQueryBuilder('a')
            ->select('a.id', 'a.title', 'a.author', 'a.createdAt', 'a.updatedAt')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery();

        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult($perPage * ($page - 1))
            ->setMaxResults($perPage);

        return $paginator->getQuery()->getResult();
    }

    /**
     * @throws ArticleNotFoundException
     */
    public function findById(int $articleId): Article
    {
        $article = $this->find($articleId);

        if (!$article) {
            throw new ArticleNotFoundException('Article with Id ' . $articleId . ' was not found!');
        }

        return $article;
    }

    public function save(Article $article): void
    {
        if ($article->getCreatedAt() === null) {
            $article->setCreatedAt(new \DateTime());
        }
        $article->setUpdatedAt(new \DateTime());

        $this->getEntityManager()->persist($article);
        $this->getEntityManager()->flush();
    }

    public function remove(Article $article): void
    {
        $this->getEntityManager()->remove($article);
        $this->getEntityManager()->flush();
    }
}
