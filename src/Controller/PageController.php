<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ContactForm;
use App\Form\NewsletterForm;
use App\Service\LandingPageService;
use App\Service\PageData;
use App\Value\Newsletter as NewsletterData;
use GProDB\LandingPage\ElementName;
use GProDB\LandingPage\Elements\Contact;
use GProDB\LandingPage\Elements\Newsletter;
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
        $mailingForm = $this->mailingForm($pageData, $pageUuid);

        return $this->render('page/index.html.twig', [
            'data' => new PageData($pageData),
            'ElementName' => ElementName::class,
            'contactForm' => $contactForm,
            'mailingForm' => $mailingForm,
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

    private function mailingForm(LandingPage $pageData, Uuid $pageUuid): FormInterface|null
    {
        /** @var Newsletter $newsletterElement */
        $newsletterElement = $pageData->getElement(ElementName::NEWSLETTER);

        if (false === $newsletterElement?->enabled ?? false) {
            return null;
        }

        return $this->createForm(
            NewsletterForm::class,
            NewsletterData::create($pageUuid),
            [
               'action' => $this->generateUrl('app_newsletter_mail'),
            ],
        );
    }
}
