<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
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

        $this->articleRepository->create($article);

        return $this->json($article, Response::HTTP_CREATED);
    }
}
