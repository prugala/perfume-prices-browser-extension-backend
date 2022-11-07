<?php

declare(strict_types=1);

namespace App\Dto;

class PriceDto
{
    public string $shopName;
    public string $url;
    public float $price;
    public float $priceChange;
}
