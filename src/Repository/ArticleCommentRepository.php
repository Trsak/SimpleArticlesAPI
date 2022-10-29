<?php

namespace App\Repository;

use App\Entity\ArticleComment;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class ArticleCommentRepository extends NestedTreeRepository
{
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct($manager, $manager->getClassMetadata(ArticleComment::class));
    }

    public function save(ArticleComment $articleComment): void
    {
        if ($articleComment->getCreatedAt() === null) {
            $articleComment->setCreatedAt(new \DateTime());
        }
        $articleComment->setUpdatedAt(new \DateTime());

        $this->getEntityManager()->remove($articleComment);
        $this->getEntityManager()->flush();
    }
}
