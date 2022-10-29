<?php

namespace App\Repository;

use App\Entity\ArticleComment;
use App\Repository\Exception\ArticleCommentNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class ArticleCommentRepository extends NestedTreeRepository
{
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct($manager, $manager->getClassMetadata(ArticleComment::class));
    }

    /**
     * @throws ArticleCommentNotFoundException
     */
    public function findById(int $articleCommentId): ArticleComment
    {
        $articleComment = $this->find($articleCommentId);

        if (!$articleComment) {
            throw new ArticleCommentNotFoundException('Article comment with Id ' . $articleId . ' was not found!');
        }

        return $articleComment;
    }

    public function getTreeByArticle(int $articleId) : array
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->from(ArticleComment::class, 'c')
            ->select('c')
            ->orderBy('c.root, c.lft', 'ASC')
            ->where('c.article = :articleId')
            ->setParameter('articleId', $articleId)
            ->getQuery();

        return $this->buildTreeArray($query->getArrayResult());
    }

    public function save(ArticleComment $articleComment): void
    {
        if ($articleComment->getCreatedAt() === null) {
            $articleComment->setCreatedAt(new \DateTime());
        }
        $articleComment->setUpdatedAt(new \DateTime());

        $this->getEntityManager()->persist($articleComment);
        $this->getEntityManager()->flush();
    }
}
