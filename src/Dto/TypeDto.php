<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enum\TypeEnum;

final class TypeDto
{
    public string $name;
    public string $url;
    public TypeEnum $code;
    /** @var SizeDto[] */
    public array $sizes;
}
