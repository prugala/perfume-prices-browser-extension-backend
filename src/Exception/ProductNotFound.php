<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class ProductNotFound extends Exception
{
    public function __construct(string $name, string $provider)
    {
        parent::__construct(sprintf('Product `%s` was not found for provider `%s`.', $name, $provider));
    }
}
