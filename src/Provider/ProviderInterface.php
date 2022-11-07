<?php

declare(strict_types=1);

namespace App\Provider;

use App\Dto\DataDto;

interface ProviderInterface
{
    public function getName(): string;

    public function getData(string $path): DataDto;

    public function search(string $name): string;
}
