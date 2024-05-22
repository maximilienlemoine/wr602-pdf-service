<?php

namespace App\Service;

use App\HttpClient\GotenbergHttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GeneratePdfService
{
    private GotenbergHttpClient $gotenbergHttpClient;

    public function __construct(GotenbergHttpClient $gotenbergHttpClient)
    {
        $this->gotenbergHttpClient = $gotenbergHttpClient;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function generatePdfFromHtml(string $pathHtmlFile): string
    {
        return $this->gotenbergHttpClient->post('/forms/chromium/convert/html', [
            'headers' => [
                'Content-Type' => 'multipart/form-data'
            ],
            'body' => [
                'files' => [
                    'file' => [
                        'name' => 'file',
                        'contents' => fopen($pathHtmlFile, 'r')
                    ],
                ],
            ]
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function generatePdfFromUrl(string $url): string
    {
        return $this->gotenbergHttpClient->post('/forms/chromium/convert/url', [
            'headers' => [
                'Content-Type' => 'multipart/form-data'
            ],
            'body' => [
                'url' => $url
            ]
        ]);
    }
}
