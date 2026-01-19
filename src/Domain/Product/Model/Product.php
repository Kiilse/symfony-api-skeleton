<?php

declare(strict_types=1);

namespace App\Domain\Product\Model;

use App\Domain\Product\ValueObject\ProductId;

final class Product
{
    private function __construct(
        private readonly ProductId $id,
        private string $name,
        private string $description,
        private float $price,
        private readonly \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt
    ) {
    }

    public static function create(
        string $name,
        string $description,
        float $price
    ): self {
        $id = ProductId::generate();
        $now = new \DateTimeImmutable();

        return new self($id, $name, $description, $price, $now, $now);
    }

    public static function reconstitute(
        ProductId $id,
        string $name,
        string $description,
        float $price,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ): self {
        return new self($id, $name, $description, $price, $createdAt, $updatedAt);
    }

    public function id(): ProductId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function price(): float
    {
        return $this->price;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateDescription(string $description): void
    {
        $this->description = $description;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updatePrice(float $price): void
    {
        $this->price = $price;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
