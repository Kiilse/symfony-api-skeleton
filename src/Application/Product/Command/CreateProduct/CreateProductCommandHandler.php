<?php

declare(strict_types=1);

namespace App\Application\Product\Command\CreateProduct;

use App\Domain\Product\Model\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Product\ValueObject\ProductId;

final readonly class CreateProductCommandHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {
    }

    public function __invoke(CreateProductCommand $command): ProductId
    {
        $product = Product::create(
            $command->name,
            $command->description,
            $command->price
        );

        $this->productRepository->save($product);

        return $product->id();
    }
}
