<?php

declare(strict_types=1);

namespace App\Application\Product\Query\GetProduct;

final readonly class GetProductQuery
{
    public function __construct(
        public string $productId
    ) {
    }
}
