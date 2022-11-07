<?php

declare(strict_types=1);

namespace App\Provider;

use App\Exception\ProviderNotFound;

final class Builder
{
    public function __construct(private readonly iterable $providers)
    {
    }

    public function getProvider(string $name): ProviderInterface
    {
        /** @var ProviderInterface $provider */
        foreach ($this->providers as $provider) {
            if ($name === $provider->getName()) {
                return $provider;
            }
        }

        throw new ProviderNotFound($name);
    }
}
