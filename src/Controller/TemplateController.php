<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TemplateController extends AbstractController
{
    #[Route('/{projectUuid}', name: 'app_project')]
    public function project(string $projectUuid): Response
    {
        return $this->json([
            'projectUuid' => $projectUuid,
        ]);
    }
}
