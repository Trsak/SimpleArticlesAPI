<?php

namespace App\Mapper;

class ArticleCommentTreeMapper
{
    public function mapTree(array $tree) : array
    {
        $resultArray = array();

        foreach ($tree as $treeItem) {
            $resultArray[] = array(
                'id' => $treeItem['id'],
                'text' => $treeItem['text'],
                'author' => $treeItem['author'],
                'authorEmail' => $treeItem['authorEmail'],
                'createdAt' => $treeItem['createdAt'],
                'updatedAt' => $treeItem['updatedAt'],
                'replies' => $this->mapTree($treeItem['__children'])
            );
        }

        return $resultArray;
    }
}
