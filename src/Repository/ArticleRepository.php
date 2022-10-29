<?php

namespace App\Repository;

use App\Entity\Article;
use App\Repository\Exception\ArticleNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @throws ArticleNotFoundException
     */
    public function findById(int $articleId): Article
    {
        $article = $this->findOneBy([
            'id' => $articleId
        ]);

        if (!$article) {
            throw new ArticleNotFoundException('Article with Id ' . $articleId . ' was not found!');
        }

        return $article;
    }

    public function create(Article $article): void
    {
        $this->getEntityManager()->persist($article);
        $this->getEntityManager()->flush();
    }

    public function remove(Article $article): void
    {
        $this->getEntityManager()->remove($article);
        $this->getEntityManager()->flush();
    }
}
