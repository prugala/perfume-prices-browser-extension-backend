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
    public string $brand;
    public string $line;
    public string $type;
    public string $gender;
    /** @var PriceDto[] */
    public array $prices = [];
}
