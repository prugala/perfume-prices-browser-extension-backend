<?php

declare(strict_types=1);

namespace App\Provider;

use App\Dto\DataDto;
use App\Enum\PageType;

interface ProviderInterface
{
    public function getName(): string;

    public function getData(string $path): DataDto;

    public function getPriceHistory(array $params): array;

    public function search(string $name, ?PageType $pageType = null, ?int $id = null): string;
}
