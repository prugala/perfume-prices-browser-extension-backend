<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ProviderNotFound;
use App\Provider\Builder;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProxyAction
{
    public function __construct(private readonly Builder $builder, private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(string $provider, string $providerData): Response
    {
        try {
            $provider = $this->builder->getProvider($provider);
            $data = $provider->getData($providerData);
        } catch (ProviderNotFound) {
            throw new NotFoundHttpException();
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage(), ['context' => $exception]);

            throw new BadRequestHttpException();
        }
        return new JsonResponse($data);
    }
}
