<?php

declare(strict_types=1);

namespace App\Domain\Product\Exception;

use App\Domain\Shared\Exception\DomainException;

final class ProductNotFoundException extends DomainException
{
    public const CODE = 'PRODUCT_NOT_FOUND';

    public static function withId(string $id): self
    {
        return new self(
            \sprintf('Product with ID "%s" not found', $id),
            self::CODE,
            404
        );
    }
}
