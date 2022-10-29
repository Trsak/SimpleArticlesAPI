<?php

namespace App\Controller;

use App\Entity\ArticleComment;
use App\Repository\ArticleCommentRepository;
use App\Repository\ArticleRepository;
use App\Repository\Exception\ArticleNotFoundException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArticleCommentController extends BaseController
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly ArticleCommentRepository $articleCommentRepository,
        private readonly ValidatorInterface $validator
    )
    {
    }

    /**
     * Add new comment to article.
     *
     * @OA\RequestBody(
     *     @Model(type=ArticleComment::class, groups={"create"})
     * )
     *
     * @OA\Response(
     *     response=201,
     *     description="Article comment was added successfully",
     *     @Model(type=ArticleComment::class)
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Bad request"
     * )
     *
     * @OA\Parameter(
     *     name="articleId",
     *     in="path",
     *     description="ID of article",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\Tag(name="Article Comments")
     */
    #[Route('/api/article/{articleId<\d+>}/addComment', methods: ['POST'])]
    public function addArticleComment(Request $request, int $articleId): JsonResponse
    {
        try {
            $article = $this->articleRepository->findById($articleId);
            $postData = json_decode($request->getContent(), false);

            $articleComment = new ArticleComment();
            $articleComment->setText($postData->text ?? null);
            $articleComment->setAuthor($postData->author ?? null);
            $articleComment->setAuthorEmail($postData->authorEmail ?? null);
            $articleComment->setArticle($article);

            $errors = $this->validator->validate($articleComment);
            if (count($errors) > 0) {
                return new JsonResponse($this->getValidationErrorsArray($errors), Response::HTTP_BAD_REQUEST);
            }

            $this->articleCommentRepository->save($articleComment);
            return $this->json($articleComment, Response::HTTP_CREATED);
        } catch (ArticleNotFoundException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }
}
