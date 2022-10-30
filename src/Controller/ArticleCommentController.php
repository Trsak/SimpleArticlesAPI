<?php

namespace App\Controller;

use App\Entity\ArticleComment;
use App\Model\ArticleCommentModel;
use App\Repository\ArticleCommentRepository;
use App\Repository\ArticleRepository;
use App\Repository\Exception\ArticleCommentNotFoundException;
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
     * Get all article comments.
     *
     * @OA\Response(
     *     response=201,
     *     description="Array of all article comments",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=ArticleCommentModel::class))
     *     )
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
    #[Route('/api/article/{articleId<\d+>}/comments', methods: ['GET'])]
    public function getArticleComments(int $articleId): JsonResponse
    {
        try {
            $article = $this->articleRepository->findById($articleId);
            $articleComments = $this->articleCommentRepository->getArticleCommentsArray($article->getId());

            return $this->json($articleComments, Response::HTTP_OK);
        } catch (ArticleNotFoundException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Add new comment to the article.
     *
     * @OA\RequestBody(
     *     @Model(type=ArticleComment::class, groups={"create"})
     * )
     *
     * @OA\Response(
     *     response=201,
     *     description="Article comment was added successfully",
     *     @Model(type=ArticleComment::class, groups={"default"})
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
     * @OA\Parameter(
     *     name="parentCommentId",
     *     required=false,
     *     in="query",
     *     description="ID of parent comment",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\Tag(name="Article Comments")
     */
    #[Route('/api/article/{articleId<\d+>}/comments/add', methods: ['POST'])]
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

            $parentCommentId = $request->get('parentCommentId');
            if ($parentCommentId) {
                $parentComment = $this->articleCommentRepository->findById($parentCommentId);
                $articleComment->setParent($parentComment);
            }

            $errors = $this->validator->validate($articleComment);
            if (count($errors) > 0) {
                return new JsonResponse($this->getValidationErrorsArray($errors), Response::HTTP_BAD_REQUEST);
            }

            $this->articleCommentRepository->save($articleComment);

            return $this->json($articleComment, Response::HTTP_CREATED);
        } catch (ArticleNotFoundException|ArticleCommentNotFoundException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Updates given Article Comment.
     *
     * @OA\Response(
     *     response=200,
     *     description="Article comment detail",
     *     @Model(type=ArticleComment::class)
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Bad request"
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="Article or comment with given Id was not found"
     * )
     *
     * @OA\Parameter(
     *     name="articleId",
     *     in="path",
     *     description="ID of article",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\Parameter(
     *     name="commentId",
     *     in="path",
     *     description="ID of parent comment",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\RequestBody(
     *     @Model(type=ArticleComment::class, groups={"update"})
     * )
     *
     * @OA\Tag(name="Article Comments")
     */
    #[Route('/api/article/{articleId<\d+>}/comments/{commentId<\d+>}/update', methods: ['PATCH'])]
    public function updateArticleComment(int $articleId, int $commentId, Request $request): Response
    {
        try {
            $this->articleRepository->findById($articleId);
            $articleComment = $this->articleCommentRepository->findById($commentId);
            $postData = json_decode($request->getContent(), false);

            $articleComment->setText($postData->text ?? null);
            $articleComment->setAuthor($postData->author ?? null);
            $articleComment->setAuthorEmail($postData->authorEmail ?? null);
            $this->articleCommentRepository->save($articleComment);
        } catch (ArticleNotFoundException|ArticleCommentNotFoundException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_OK);
        }

        return $this->json($articleComment, Response::HTTP_OK);
    }

    /**
     * Removes given Article Comment.
     *
     * @OA\Response(
     *     response=200,
     *     description="Article comment was removed successfully"
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Bad request"
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="Article or comment with given Id was not found"
     * )
     *
     * @OA\Parameter(
     *     name="articleId",
     *     in="path",
     *     description="ID of article",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\Parameter(
     *     name="commentId",
     *     in="path",
     *     description="ID of comment",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\Tag(name="Article Comments")
     */
    #[Route('/api/article/{articleId<\d+>}/comments/{commentId<\d+>}/remove', methods: ['DELETE'])]
    public function removeArticle(int $articleId, int $commentId): Response
    {
        try {
            $this->articleRepository->findById($articleId);

            $articleComment = $this->articleCommentRepository->findById($commentId);
            $this->articleCommentRepository->remove($articleComment);
        } catch (ArticleCommentNotFoundException|ArticleNotFoundException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_NOT_FOUND);
        }

        return $this->json('Article comment was removed.', Response::HTTP_OK);
    }
}
