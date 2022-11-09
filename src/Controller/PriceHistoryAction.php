<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ProviderNotFound;
use App\Provider\Builder;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PriceHistoryAction
{
    public function __construct(private readonly Builder $builder, private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(Request $request, string $provider): Response
    {
        try {
            $provider = $this->builder->getProvider($provider);
            $data = $provider->getPriceHistory($request->query->all());
        } catch (ProviderNotFound) {
            throw new NotFoundHttpException();
        } catch (\Throwable $exception) {
            dd($exception->getMessage());
            $this->logger->error($exception->getMessage(), ['context' => $exception]);

            throw new BadRequestHttpException();
        }
        return new JsonResponse($data);
    }
}
