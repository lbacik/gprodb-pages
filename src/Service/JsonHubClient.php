<?php

declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\Client;
use OpenAPI\Client\Api\EntityApi;
use OpenAPI\Client\Configuration;
use OpenAPI\Client\HeaderSelector;
use OpenAPI\Client\Model\EntityJsonldEntityReadEntityReadParent;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Uid\Uuid;

class JsonHubClient
{
    public function __construct(
        #[Autowire(env: "JSON_HUB_API")]
        private readonly string $jsonHubApi,
    ) {
    }

    public function getPageData(Uuid $entityId): EntityJsonldEntityReadEntityReadParent
    {
        $data = new EntityApi(new Client(), $this->getConfig())
            ->apiEntitiesIdGet($entityId->toRfc4122());

        return $data;
    }

    /** @return EntityJsonldEntityReadEntityReadParent[] */
    public function getPages(string $definitionUuid): array
    {
        $client = new EntityApi(
            client: new Client(),
            config: $this->getConfig(),
            selector: new class() extends HeaderSelector {
                public function selectHeaders(array $accept, string $contentType, bool $isMultipart): array
                {
                    return [
                        'Accept' => 'application/ld+json',
                        'Content-Type' => 'application/json',
                    ];
                }
            }
        );

        $data = $client
            ->apiEntitiesGetCollection(definition: $definitionUuid);

        return $data->getHydraMember();
    }

    private function getConfig(string|null $token = null): Configuration
    {
        $config = Configuration::getDefaultConfiguration()
            ->setHost($this->jsonHubApi);

        if ($token) {
            $config->setAccessToken($token);
        }

        return $config;
    }
}
