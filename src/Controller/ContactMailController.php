<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ContactForm;
use App\Service\ContactMailService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactMailController extends AbstractController
{
    #[Route('/contact-mail', name: 'app_contact_mail', methods: ['POST'])]
    public function index(
        Request $request,
        ContactMailService $contactMailService,
        TranslatorInterface $translator,
        LoggerInterface $logger,
    ): Response {
        $form = $this->createForm(
            ContactForm::class,
            null,
            [
                'action' => $this->generateUrl('app_contact_mail'),
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $contactMailService->send($data);
                return $this->render('partial/_contact-form.html.twig', [
                    'contactForm' => $this->createForm(ContactForm::class)
                ]);
            } catch (\Throwable $throwable) {
                $form->addError(new FormError($translator->trans('contact.send_error')));

                $logger->error('Contact send error: ' . $throwable->getMessage(), $throwable->getTrace());

                return $this->render(
                    'partial/_contact-form.html.twig',
                    [
                        'contactForm' => $form->createView(),
                    ],
                    new Response(null, Response::HTTP_INTERNAL_SERVER_ERROR)
                );
            }
        }

        return $this->render(
            'partial/_contact-form.html.twig',
            [
                'contactForm' => $form->createView(),
            ],
            new Response(null, Response::HTTP_BAD_REQUEST)
        );
    }
}
