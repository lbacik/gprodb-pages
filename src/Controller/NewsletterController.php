<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\NewsletterForm;
use App\Service\MailingListService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class NewsletterController extends AbstractController
{
    #[Route('/newsletter', name: 'app_newsletter_mail', methods: ['POST'])]
    public function __invoke(
        Request $request,
        MailingListService $mailingListService,
        TranslatorInterface $translator,
        LoggerInterface $logger,
    ): Response {
        $form = $this->createForm(
            NewsletterForm::class,
            null,
            [
                'action' => $this->generateUrl('app_newsletter_mail'),
            ],
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $mailingListService->subscribe($form->getData());
                return $this->render(
                    'partial/_newsletter-form.html.twig',
                    [
                        'mailingForm' => $this->createForm(NewsletterForm::class),
                    ],
                );
            } catch (\Throwable $throwable) {
                $form->addError(new FormError($translator->trans('contact.send_error')));

                $logger->error('Mailing subscription error: ' . $throwable->getMessage(), $throwable->getTrace());

                return $this->render(
                    'partial/_newsletter-form.html.twig',
                    [
                        'mailingForm' => $form,
                    ],
                    new Response(null, Response::HTTP_INTERNAL_SERVER_ERROR)
                );
            }
        }

        return $this->render(
            'partial/_newsletter-form.html.twig',
            [
                'mailingForm' => $form->createView(),
            ],
            new Response(null, Response::HTTP_BAD_REQUEST)
        );
    }
}
