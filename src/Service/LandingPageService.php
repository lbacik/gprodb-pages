<?php

declare(strict_types=1);

namespace App\Service;

use GProDB\LandingPage\LandingPage;
use GProDB\LandingPage\MapperFactory;
use GProDB\LandingPage\Mappers\LandingPageMapperEnum;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\Uuid;

class LandingPageService
{
    private MapperFactory $mapperFactory;

    public function __construct(
        private JsonHubClient $client,
        #[Autowire(env: "PAGE_V1")] private string $V1,
        #[Autowire(env: "PAGE_V2")] private string $V2,
        #[Autowire(env: "PAGE_V3")] private string $V3,
        private readonly NormalizerInterface $normalizer,
    ) {
        $this->mapperFactory = new MapperFactory();
    }

    public function get(Uuid $id): LandingPage
    {
        $entity = $this->client->getPageData($id);
        $entityArray = $this->normalizer->normalize($entity->getData());

        $version = match ($entity->getDefinition()->getId()) {
            $this->V1 => LandingPageMapperEnum::PROJECT_V1,
            $this->V2 => LandingPageMapperEnum::PROJECT_V2,
            $this->V3 => LandingPageMapperEnum::PROJECT_V3,
            default => null,
        };

        if ($version === null) {
            throw new \RuntimeException('Unknown landing page version');
        }

        return $this->mapperFactory
            ->createArrayToLandingPage()
            ->map($entityArray, $version);
    }
}
