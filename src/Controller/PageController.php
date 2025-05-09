<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\LandingPageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class PageController extends AbstractController
{
    #[Route('/{pageUuid}', name: 'app_page')]
    public function page(
        Uuid $pageUuid,
        LandingPageService $landingPageService,
    ): Response {
        $pageData = $landingPageService->get($pageUuid);

        return $this->json($pageData);
    }
}
