<?php

namespace App\Mapper;

use App\Model\ArticleCommentModel;

class ArticleCommentTreeMapper
{
    public function mapArrayTreeToArticleCommentArray(array $tree): array
    {
        $commentsArray = array();

        foreach ($tree as $treeItem) {
            $articleComment = new ArticleCommentModel();
            $articleComment->id = $treeItem['id'];
            $articleComment->text = $treeItem['text'];
            $articleComment->author = $treeItem['author'];
            $articleComment->authorEmail = $treeItem['authorEmail'];
            $articleComment->createdAt = $treeItem['createdAt'];
            $articleComment->updatedAt = $treeItem['updatedAt'];
            $articleComment->replies = $this->mapArrayTreeToArticleCommentArray($treeItem['__children']);

            $commentsArray[] = $articleComment;
        }

        return $commentsArray;
    }
}
