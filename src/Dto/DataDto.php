<?php

declare(strict_types=1);

namespace App\Dto;

final class DataDto
{
    public string $provider;
    /** @var TypeDto[] */
    public array $types = [];
}
