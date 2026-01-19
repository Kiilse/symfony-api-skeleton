<?php

declare(strict_types=1);

namespace App\Application\Product\Query\GetProduct;

use App\Application\Product\DTO\ProductResponseDTO;
use App\Domain\Product\Exception\ProductNotFoundException;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Product\ValueObject\ProductId;

final readonly class GetProductQueryHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {
    }

    public function __invoke(GetProductQuery $query): ProductResponseDTO
    {
        $productId = ProductId::fromString($query->productId);
        $product = $this->productRepository->findById($productId);

        if (!$product) {
            throw ProductNotFoundException::withId($query->productId);
        }

        return new ProductResponseDTO(
            productId: $product->id()->value(),
            name: $product->name(),
            description: $product->description(),
            price: $product->price(),
            createdAt: $product->createdAt()->format(\DATE_ATOM),
            updatedAt: $product->updatedAt()->format(\DATE_ATOM)
        );
    }
}
