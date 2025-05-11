<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\LandingPageService;
use App\Service\PageData;
use GProDB\LandingPage\ElementName;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

class PageController extends AbstractController
{
    #[Route('/{pageUuid}', name: 'app_page')]
    public function page(
        Uuid $pageUuid,
        LandingPageService $landingPageService,
        #[MapQueryParameter] string|null $format = null,
    ): Response {
        $pageData = $landingPageService->get($pageUuid);

        if ($format === 'json') {
            return $this->json($pageData);
        }

        return $this->render('page/index.html.twig', [
            'data' => new PageData($pageData),
            'ElementName' => ElementName::class,
        ]);
    }
}
