<?php

namespace App\Model;

use Doctrine\Common\Collections\Collection;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class ArticleCommentModel
{
    /**
     * @OA\Property(description="Article comment Id")
     */
    public int $id;

    /**
     * @OA\Property(description="Comment text")
     */
    public string $text;

    /**
     * @OA\Property(description="Comment author")
     */
    public string $author;

    /**
     * @OA\Property(description="Comment author email")
     */
    public string $authorEmail;

    /**
     * @OA\Property(description="Date and time of creation")
     */
    public \DateTime $createdAt;

    /**
     * @OA\Property(description="Date and time of last update")
     */
    public \DateTime $updatedAt;

    /**
     * @OA\Property(
     *     description="Replies to the comment",
     *     type="array",
     *     @OA\Items(ref=@Model(type=ArticleCommentModel::class))
     * )
     */
    public array $replies;
}
