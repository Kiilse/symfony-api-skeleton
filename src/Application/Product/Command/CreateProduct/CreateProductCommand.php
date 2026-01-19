<?php

declare(strict_types=1);

namespace App\Application\Product\Command\CreateProduct;

final readonly class CreateProductCommand
{
    public function __construct(
        public string $name,
        public string $description,
        public float $price
    ) {
    }
}
