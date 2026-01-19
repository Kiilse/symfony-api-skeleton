<?php

declare(strict_types=1);

namespace App\Infrastructure\Product\Persistence\Doctrine;

use App\Domain\Product\Model\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Product\ValueObject\ProductId;
use Doctrine\DBAL\Connection;

final readonly class DoctrineProductRepository implements ProductRepositoryInterface
{
    private const TABLE = 'products';

    public function __construct(
        private Connection $connection
    ) {
    }

    public function save(Product $product): void
    {
        $data = [
            'id' => $product->id()->value(),
            'name' => $product->name(),
            'description' => $product->description(),
            'price' => $product->price(),
            'updated_at' => $product->updatedAt()->format('Y-m-d H:i:s'),
        ];

        $exists = $this->connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from(self::TABLE)
            ->where('id = :id')
            ->setParameter('id', $product->id()->value())
            ->fetchOne() > 0;

        if ($exists) {
            $this->connection->update(self::TABLE, $data, ['id' => $product->id()->value()]);
        } else {
            $data['created_at'] = $product->createdAt()->format('Y-m-d H:i:s');
            $this->connection->insert(self::TABLE, $data);
        }
    }

    public function findById(ProductId $productId): ?Product
    {
        $row = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('id = :id')
            ->setParameter('id', $productId->value())
            ->fetchAssociative();

        if (!$row) {
            return null;
        }

        return Product::reconstitute(
            ProductId::fromString($row['id']),
            $row['name'],
            $row['description'],
            (float) $row['price'],
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $row['created_at']),
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $row['updated_at'])
        );
    }

    public function delete(Product $product): void
    {
        $this->connection->delete(self::TABLE, ['id' => $product->id()->value()]);
    }
}
