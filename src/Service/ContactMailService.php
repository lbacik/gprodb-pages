<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ContactMailService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        #[Autowire(env: 'INTERNAL_API_URL')]
        private readonly string $internalApiUrl,
        #[Autowire(env: 'INTERNAL_API_TOKEN')]
        private readonly string $internalApiToken,
        #[Autowire(env: 'EMAIL_SENDER_ADDRESS')]
        private readonly string $emailSenderAddress,
    ) {
    }

    public function send(array $contact): void
    {
        $response = $this->httpClient->request(
            'POST',
            $this->internalApiUrl . '/api/messages',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->internalApiToken,
                ],
                'json' => [
                    'entityId' => $contact['entityId'],
                    'senderSystemAddress'=> $this->emailSenderAddress,
                    'senderName' => $contact['name'],
                    'senderEmail' => $contact['email'],
                    'subject' => $contact['subject'],
                    'message' => $contact['message'],
                ],
            ]
        );

        if ($response->getStatusCode() !== Response::HTTP_NO_CONTENT) {
            throw new \RuntimeException('Unable to send message');
        }
    }
}
