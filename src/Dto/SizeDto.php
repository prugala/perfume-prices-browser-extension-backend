<?php

declare(strict_types=1);

namespace App\Dto;

final class SizeDto
{
    public float $size;
    public bool $tester;
    public bool $set;
    public float $price;
    public float $priceChange;
    /** @var PriceDto[] */
    public array $prices = [];
}
