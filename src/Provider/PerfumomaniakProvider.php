<?php

declare(strict_types=1);

namespace App\Provider;

use App\Dto\DataDto;
use App\Dto\PriceDto;
use App\Dto\SizeDto;
use App\Dto\TypeDto;
use App\Entity\ProductLink;
use App\Enum\PageType;
use App\Enum\TypeEnum;
use App\Exception\ProductNotFound;
use App\Repository\ProductLinkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PerfumomaniakProvider implements ProviderInterface
{
    private const NAME = 'perfumomaniak.pl';
    private const HOST = 'https://perfumomaniak.pl';

    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly ProductLinkRepository $productLinkRepository, private HttpClientInterface $client)
    {
        $this->client = $this->client->withOptions(['base_uri' => self::HOST]);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function search(string $name, ?PageType $pageType = null, ?int $id = null): string
    {
        if ($pageType && $id) {
            $productLink = $this->productLinkRepository->findOneBy([
                'active' => true,
                'provider' => self::NAME,
                'productId' => $id,
                'page' => $pageType->value
            ]);

            if ($productLink) {
                return $productLink->getUrl();
            }
        }

        $response = $this->client->request(Request::METHOD_GET, sprintf('api/perfumes/%s', $name));

        try {
            $data = $response->toArray();

            return (string)$data['id'];
        } catch (\Throwable) {
            throw new ProductNotFound($name, self::NAME);
        }
    }

    public function getData(string $providerData): DataDto
    {
        $data = new DataDto();
        $data->provider = self::NAME;

        $response = $this->client->request(Request::METHOD_GET, sprintf('/api/pricelist/%s', $providerData));

        $data->types = $this->prepareTypes($response->toArray()['Types']);

        return $data;
    }

    public function getPriceHistory(array $params): array
    {
        $params = array_merge([
            'mode' => 'product',
        ], $params);

        return $this->client->request(Request::METHOD_GET, 'price-history', [
            'query' => $params,
        ])->toArray();
    }

    public function reportLink(int $id, PageType $pageType, string $url): void
    {
        $link = new ProductLink();
        $link
            ->setPage($pageType->value)
            ->setProductId($id)
            ->setProvider($this->getName())
            ->setUrl($url);

        $this->entityManager->persist($link);
        $this->entityManager->flush();
    }

    /**
     * @param array $types
     * @return TypeDto[]|array
     */
    private function prepareTypes(array $types): array
    {
        $data = [];

        foreach ($types as $type) {
            $typeDto = new TypeDto();
            $typeDto->code = TypeEnum::tryFrom(strtolower($type['Code']));
            $typeDto->name = $typeDto->code->name();
            $typeDto->url = $type['Url'];
            $typeDto->sizes = $this->prepareSizes($type['Sizes']);
            $data[] = $typeDto;
        }

        return $data;
    }

    /**
     * @param array $sizes
     * @return SizeDto[]|array
     */
    private function prepareSizes(array $sizes): array
    {
        $data = [];

        foreach ($sizes as $size) {
            $data[] = $this->createSize($size);
        }

        return $data;
    }

    private function createSize(array $product): SizeDto
    {
        $sizeDto = new SizeDto();
        $sizeDto->size = $product['Size'];
        $sizeDto->tester = $product['Tester'];
        $sizeDto->set = false;
        $sizeDto->price = $product['Price'];
        $sizeDto->priceChange = 0;
        $sizeDto->brand = $product['Brand'];
        $sizeDto->line = $product['Line'];
        $sizeDto->type = $product['Type'];
        $sizeDto->gender = $product['Gender'];

        foreach ($product['Prices'] as $offer) {
            $priceDto = new PriceDto();
            $priceDto->shopName = $offer['ShopName'];
            $priceDto->url = $offer['Url'];
            $priceDto->price = $offer['Price'];
            $priceDto->priceChange = 0;
            $sizeDto->prices[] = $priceDto;
        }

        return $sizeDto;
    }
}
