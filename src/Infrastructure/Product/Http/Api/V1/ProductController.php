<?php

declare(strict_types=1);

namespace App\Infrastructure\Product\Http\Api\V1;

use App\Application\Product\Command\CreateProduct\CreateProductCommand;
use App\Application\Product\Command\CreateProduct\CreateProductCommandHandler;
use App\Application\Product\Query\GetProduct\GetProductQuery;
use App\Application\Product\Query\GetProduct\GetProductQueryHandler;
use App\Domain\Shared\Exception\DomainException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1/products', name: 'api_v1_products_')]
final readonly class ProductController
{
    public function __construct(
        private CreateProductCommandHandler $createProductHandler,
        private GetProductQueryHandler $getProductHandler,
        private ValidatorInterface $validator
    ) {
    }

    /**
     * POST /api/v1/products
     * Create a new product.
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!\is_array($data)) {
            return new JsonResponse(
                ['error' => 'Invalid JSON'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $constraints = new Collection([
            'name' => [new NotBlank(), new Length(min: 1, max: 255)],
            'description' => [new NotBlank(), new Length(min: 1, max: 1000)],
            'price' => [new NotBlank(), new Positive()],
        ]);

        $violations = $this->validator->validate($data, $constraints);

        if (\count($violations) > 0) {
            $errors = [];

            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return new JsonResponse(
                ['errors' => $errors],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $command = new CreateProductCommand(
                name: $data['name'],
                description: $data['description'],
                price: (float) $data['price']
            );

            $productId = ($this->createProductHandler)($command);

            return new JsonResponse(
                ['id' => $productId->value()],
                Response::HTTP_CREATED,
                ['Location' => "/api/v1/products/{$productId->value()}"]
            );
        } catch (DomainException $e) {
            return new JsonResponse(
                [
                    'error' => $e->getMessage(),
                    'code' => $e->getErrorCode(),
                ],
                $e->getHttpStatusCode()
            );
        } catch (\Throwable $e) {
            return new JsonResponse(
                [
                    'error' => $e->getMessage(),
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * GET /api/v1/products/{id}
     * Get product by id.
     */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(string $id): JsonResponse
    {
        if (empty(trim($id))) {
            return new JsonResponse(
                ['error' => 'Product ID is required'],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $query = new GetProductQuery(productId: $id);
            $product = ($this->getProductHandler)($query);

            return new JsonResponse($product->jsonSerialize());
        } catch (DomainException $e) {
            return new JsonResponse(
                [
                    'error' => $e->getMessage(),
                    'code' => $e->getErrorCode(),
                ],
                $e->getHttpStatusCode()
            );
        }
    }
}
