<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\Exception\ArticleNotFoundException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArticleController extends BaseController
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly ValidatorInterface $validator
    )
    {
    }

    /**
     * Returns all articles
     *
     * @OA\Response(
     *     response=200,
     *     description="Article detail",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Article::class, groups={"list"}))
     *     )
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Bad request"
     * )
     *
     * @OA\Tag(name="Articles")
     */
    #[Route('/api/articles', methods: ['GET'])]
    public function getArticles(): JsonResponse
    {
        $articles = $this->articleRepository->findAll();
        return $this->json($articles, Response::HTTP_OK);
    }

    /**
     * Returns Article by id.
     *
     * @OA\Response(
     *     response=200,
     *     description="Article detail",
     *     @Model(type=Article::class)
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Bad request"
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="Article with given Id was not found"
     * )
     *
     * @OA\Parameter(
     *     name="articleId",
     *     in="path",
     *     description="ID of article",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\Tag(name="Articles")
     */
    #[Route('/api/article/{articleId<\d+>}', methods: ['GET'])]
    public function getArticle(int $articleId): JsonResponse
    {
        try {
            $article = $this->articleRepository->findById($articleId);
            return $this->json($article, Response::HTTP_OK);
        } catch (ArticleNotFoundException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Create new Article.
     *
     * @OA\RequestBody(
     *     @Model(type=Article::class, groups={"create"})
     * )
     *
     * @OA\Response(
     *     response=201,
     *     description="Article was created successfully",
     *     @Model(type=Article::class)
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Bad request"
     * )
     *
     * @OA\Tag(name="Articles")
     */
    #[Route('/api/article/create', methods: ['POST'])]
    public function createArticle(Request $request): JsonResponse
    {
        $postData = json_decode($request->getContent(), false);

        $article = new Article();
        $article->setTitle($postData->title ?? null);
        $article->setText($postData->text ?? null);
        $article->setAuthor($postData->author ?? null);

        $errors = $this->validator->validate($article);
        if (count($errors) > 0) {
            return new JsonResponse($this->getValidationErrorsArray($errors), Response::HTTP_BAD_REQUEST);
        }

        $this->articleRepository->save($article);

        return $this->json($article, Response::HTTP_CREATED);
    }

    /**
     * Removes given Article.
     *
     * @OA\Response(
     *     response=200,
     *     description="Article was removed successfully"
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Bad request"
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="Article with given Id was not found"
     * )
     *
     * @OA\Parameter(
     *     name="articleId",
     *     in="path",
     *     description="ID of article",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\Tag(name="Articles")
     */
    #[Route('/api/article/{articleId<\d+>}/remove', methods: ['DELETE'])]
    public function removeArticle(int $articleId): Response
    {
        try {
            $article = $this->articleRepository->findById($articleId);
            $this->articleRepository->remove($article);
        } catch (ArticleNotFoundException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_NOT_FOUND);
        }

        return $this->json('Article was removed.', Response::HTTP_OK);
    }

    /**
     * Updates given Article.
     *
     * @OA\Response(
     *     response=200,
     *     description="Article detail",
     *     @Model(type=Article::class)
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Bad request"
     * )
     *
     * @OA\Response(
     *     response=404,
     *     description="Article with given Id was not found"
     * )
     *
     * @OA\Parameter(
     *     name="articleId",
     *     in="path",
     *     description="ID of article",
     *     @OA\Schema(type="integer")
     * )
     *
     * @OA\RequestBody(
     *     @Model(type=Article::class, groups={"update"})
     * )
     *
     * @OA\Tag(name="Articles")
     */
    #[Route('/api/article/{articleId<\d+>}/update', methods: ['PATCH'])]
    public function updateArticle(int $articleId, Request $request): Response
    {
        try {
            $article = $this->articleRepository->findById($articleId);
            $postData = json_decode($request->getContent(), false);

            $article->setTitle($postData->title ?? $article->getTitle());
            $article->setText($postData->text ?? $article->getText());
            $article->setAuthor($postData->author ?? $article->getAuthor());
            $this->articleRepository->save($article);
        } catch (ArticleNotFoundException $exception) {
            return $this->json($exception->getMessage(), Response::HTTP_OK);
        }

        return $this->json($article, Response::HTTP_OK);
    }
}
