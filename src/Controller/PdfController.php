<?php

namespace App\Controller;

use App\Service\GeneratePdfService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PdfController extends AbstractController
{
    private GeneratePdfService $generatePdfService;
    private string $publicTempAbsoluteDirectory;

    public function __construct(GeneratePdfService $generatePdfService, string $publicTempAbsoluteDirectory)
    {
        $this->generatePdfService = $generatePdfService;
        $this->publicTempAbsoluteDirectory = $publicTempAbsoluteDirectory;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/pdf/generate/file', name: 'html_generate_pdf', methods: ['POST'])]
    public function generatePdfFromHtml(Request $request): StreamedResponse
    {
        $file = $request->files->get('file');

        if (!is_dir($this->publicTempAbsoluteDirectory)) {
            mkdir($this->publicTempAbsoluteDirectory, 0777, true);
        }

        $subDirectory = $this->publicTempAbsoluteDirectory . '/' . uniqid();
        mkdir($subDirectory, 0777, true);

        $file->move($subDirectory, 'index.html');
        $filePath = $subDirectory.'/'.'index.html';

        chmod($filePath, 0777);
        $content = $this->generatePdfService->generatePdfFromHtml($filePath);
        unlink($filePath); // Supprimer le fichier et le dossier après utilisation

        return new StreamedResponse(function () use ($content) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="file.pdf"');

            echo $content;
        });
    }

    #[Route('/pdf/generate/url', name: 'url_generate_pdf', methods: ['POST'])]
    public function generatePdfFromUrl(Request $request): StreamedResponse
    {
        $url = $request->get('url');

        return new StreamedResponse(function () use ($url) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="file.pdf"');

            echo $this->generatePdfService->generatePdfFromUrl($url);
        });
    }
}
