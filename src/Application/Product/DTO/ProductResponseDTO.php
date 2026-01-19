<?php

declare(strict_types=1);

namespace App\Application\Product\DTO;

final readonly class ProductResponseDTO implements \JsonSerializable
{
    public function __construct(
        public string $productId,
        public string $name,
        public string $description,
        public float $price,
        public string $createdAt,
        public string $updatedAt
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->productId,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
