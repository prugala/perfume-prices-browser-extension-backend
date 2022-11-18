<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\PageType;
use App\Exception\ProviderNotFound;
use App\Provider\Builder;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReportLinkAction
{
    public function __construct(private readonly Builder $builder, private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(Request $request, string $provider): Response
    {
        try {
            $provider = $this->builder->getProvider($provider);
            $data = json_decode($request->getContent(), true);
            $provider->reportLink($data['id'], PageType::tryFrom($data['page']) ?? '', $data['url']);
        } catch (ProviderNotFound) {
            throw new NotFoundHttpException();
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage(), ['context' => $exception]);

            throw new BadRequestHttpException();
        }
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
