<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ContactForm;
use App\Service\LandingPageService;
use App\Service\PageData;
use GProDB\LandingPage\ElementName;
use GProDB\LandingPage\Elements\Contact;
use GProDB\LandingPage\LandingPage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
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

        $contactForm = $this->contactForm($pageData, $pageUuid);

        return $this->render('page/index.html.twig', [
            'data' => new PageData($pageData),
            'ElementName' => ElementName::class,
            'contactForm' => $contactForm,
        ]);
    }

    private function contactForm(LandingPage $pageData, Uuid $entityId): FormInterface|null
    {
        /** @var Contact $contactElement */
        $contactElement = $pageData->getElement(ElementName::CONTACT);

        if (false === $contactElement?->enabled ?? false) {
            return null;
        }

        return $this->createForm(
            ContactForm::class,
            [
                'entityId' => $entityId,
            ],
            [
                'action' => $this->generateUrl('app_contact_mail'),
            ]
        );
    }
}
