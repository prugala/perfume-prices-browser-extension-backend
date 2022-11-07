<?php

declare(strict_types=1);

namespace App\Enum;

enum TypeEnum: string
{
    case EDP = 'edp';
    case EDT = 'edt';
    case EDC = 'edc';
    case EP = 'ep';
    case AP = 'ap';

    public function name(): string
    {
        return match ($this) {
            TypeEnum::EDP => 'Perfume water',
            TypeEnum::EDT => 'Toilet water',
            TypeEnum::EDC => 'Cologne',
            TypeEnum::EP => 'Perfume',
            TypeEnum::AP => 'Aftershave',
        };
    }
}
