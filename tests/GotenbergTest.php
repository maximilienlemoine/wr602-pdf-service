<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GotenbergTest extends WebTestCase
{

    public function testUrlGenerate(): void
    {
        $client = static::createClient();

        try {
            $client->request('POST', '/pdf/generate/url', [
                'url' => 'https://www.google.com'
            ]);

            $this->assertResponseIsSuccessful();
        } catch (\Exception $e) {
            $this->fail('An exception was thrown during the test: ' . $e->getMessage());
        }
    }
}
