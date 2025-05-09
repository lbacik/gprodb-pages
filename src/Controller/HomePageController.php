<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\JsonHubClient;
use OpenAPI\Client\Model\EntityJsonldEntityReadEntityReadParent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    public function __construct(
        #[Autowire(env: "PAGE_V1")] private string $V1,
        #[Autowire(env: "PAGE_V2")] private string $V2,
        #[Autowire(env: "PAGE_V3")] private string $V3,
        #[Autowire(env: "DEFAULT_PROJECT_UUID")] private readonly string $defaultProjectUuid,
        #[Autowire(env: "JSON_HUB_API")]private readonly string $jsonHubUrl,
        private readonly JsonHubClient $jsonHubClient,
    ) {
    }

    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        if (empty($this->defaultProjectUuid)) {
            return $this->redirectToRoute('app_index');
        }

        return $this->redirectToRoute(
            'app_page',
            [
                'pageUuid' => $this->defaultProjectUuid,
            ],
            301
        );
    }

    #[Route('/index', name: 'app_index')]
    public function index(): Response
    {
        $result = $this->getAllEntities();

        return $this->render('homepage/index.html.twig', [
            'result' => $result,
            'server' => $this->jsonHubUrl,
        ]);
    }

    private function getAllEntities(): array
    {
        $definitions = [
            'v1' => $this->V1,
            'v2' => $this->V2,
            'v3' => $this->V3,
        ];

        $result = [];

        foreach ($definitions as $key => $definition) {
            $data = $this->jsonHubClient->getPages($definition);

            $result["{$key} {$definition}"] = array_map(
                fn (EntityJsonldEntityReadEntityReadParent $item) => [
                    'name' => $item->getData()->name ?? $item->getData()->meta?->name ?? '',
                    'slug' => $item->getSlug(),
                    'id' => $item->getId(),
                ],
                $data,
            );
        }

        return $result;
    }
}
