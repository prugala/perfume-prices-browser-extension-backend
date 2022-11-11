<?php

declare(strict_types=1);

namespace App\Provider;

use App\Dto\DataDto;
use App\Dto\PriceDto;
use App\Dto\SizeDto;
use App\Dto\TypeDto;
use App\Enum\TypeEnum;
use App\Exception\ProductNotFound;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PerfumehubProvider implements ProviderInterface
{
    private const NAME = 'perfumehub.pl';
    private const HOST = 'https://perfumehub.pl';

    public function __construct(private HttpClientInterface $client)
    {
        $this->client = $this->client->withOptions(['base_uri' => self::HOST, 'headers' => ['X-Requested-With' => 'XMLHttpRequest']]);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function search(string $name): string
    {
        $response = $this->client->request(Request::METHOD_GET, sprintf('/typeahead?q=%s', $name));
        $data = $response->toArray();
        $nameParts = explode(' ', $name);

        foreach ($data as $datum) {
            foreach ($nameParts as $namePart) {
                if (str_contains($datum['line'], $namePart)) {
                    return $datum['productLink'];
                }
            }
        }

        if (count($data) > 0) {
            return $data[0]['productLink'];
        }

        throw new ProductNotFound($name, self::NAME);
    }

    public function getData(string $path): DataDto
    {
        $data = new DataDto();
        $data->provider = self::NAME;

        $response = $this->client->request(Request::METHOD_GET, $path);

        $data->types = $this->prepareTypes($response->toArray()['typeLinks']);

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

    /**
     * @param array $types
     * @return TypeDto[]|array
     */
    private function prepareTypes(array $types): array
    {
        $data = [];

        foreach ($types as $type) {
            $typeDto = new TypeDto();
            $typeDto->code = TypeEnum::tryFrom(strtolower($type['type']));
            $typeDto->name = $typeDto->code->name();
            $typeDto->url = $type['url'];
            $typeDto->sizes = $this->prepareSizes($type['url']);
            $data[] = $typeDto;
        }

        return $data;
    }

    /**
     * @param string $uri
     * @return SizeDto[]|array
     */
    private function prepareSizes(string $uri): array
    {
        $data = [];

        $response = $this->client->request(Request::METHOD_GET, $uri);

        foreach ($response->toArray()['products'] as $product) {
            if (array_key_exists('offers', $product) && $product['size']) {
                $data[] = $this->createSize($product);
            } else {
                $subResponse = $this->client->request(Request::METHOD_GET, $product['productLink']);

                foreach ($subResponse->toArray()['products'] as $subProduct) {
                    if (array_key_exists('offers', $subProduct) && $subProduct['size']) {
                        $data[] = $this->createSize($subProduct);
                    }
                }
            }
        }

        return $data;
    }

    private function createSize(array $product): SizeDto
    {
        $sizeDto = new SizeDto();
        $sizeDto->size = $product['size'];
        $sizeDto->tester = $product['tester'];
        $sizeDto->set = $product['isSet'];
        $sizeDto->price = $product['price'];
        $sizeDto->priceChange = $product['priceChange'];
        $sizeDto->brand = $product['brand'];
        $sizeDto->line = $product['line'];
        $sizeDto->type = $product['type'];
        $sizeDto->gender = $product['gender'];

        foreach ($product['offers'] as $offer) {
            $priceDto = new PriceDto();
            $priceDto->shopName = $offer['shopNameReal'];
            $priceDto->url = $offer['url'];
            $priceDto->price = $offer['price'];
            $priceDto->priceChange = $offer['priceChange'];
            $sizeDto->prices[] = $priceDto;
        }

        return $sizeDto;
    }
}
