<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class ProviderNotFound extends Exception
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('Provider `%s` was not found.', $name));
    }
}
