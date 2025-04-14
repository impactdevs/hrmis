<?php

namespace App\Mail\Transport;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Mime\MessageConverter;

class InfobipTransport extends AbstractTransport
{
    protected $baseUrl;
    protected $apiKey;
    protected $emailFrom;

    public function __construct(string $baseUrl, string $apiKey, string $emailFrom)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
        $this->emailFrom = $emailFrom;

        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        /** @var Email $email */
        $email = MessageConverter::toEmail($message->getOriginalMessage());
    
        $multipart = [
            ['name' => 'from', 'contents' => $this->emailFrom],
            ['name' => 'to', 'contents' => implode(',', array_map(fn($a) => $a->getAddress(), $email->getTo()))],
            ['name' => 'subject', 'contents' => $email->getSubject()],
        ];
    
        if ($email->getTextBody()) {
            $multipart[] = ['name' => 'text', 'contents' => $email->getTextBody()];
        }
    
        if ($email->getHtmlBody()) {
            $multipart[] = ['name' => 'html', 'contents' => $email->getHtmlBody()];
        }
    
    // Handle Attachments
    foreach ($email->getAttachments() as $attachment) {
        if ($attachment->getDisposition() !== 'attachment') {
            continue;
        }

        $body = $attachment->getBody();
        $filename = $attachment->getFilename();
        $contentType = $attachment->getContentType();

        // Get raw content
        if (is_object($body) && method_exists($body, 'rewind')) {
            $body->rewind();
            $contents = $body->getContents();
        } else {
            $contents = $body;
        }

        $multipart[] = [
            'name' => 'attachment',
            'contents' => $contents,
            'filename' => $filename,
            'headers' => [
                'Content-Type' => $contentType,
            ]
        ];

    } 

        $response = Http::withHeaders([
            'Authorization' => 'App ' . $this->apiKey,
            'Accept' => 'application/json',
        ])
        ->asMultipart()
        ->post("{$this->baseUrl}/email/3/send", $multipart);
    
        if ($response->failed()) {
            throw new \RuntimeException("Infobip API Error: " . $response->body());
        }
    }
    

    private function formatAddresses(array $addresses): array
    {
        return array_map(function ($address) {
            return ['to' => $address->getAddress()];
        }, $addresses);
    }

    private function formatAttachments(array $attachments): array
    {
        return array_map(function (DataPart $attachment) {
            return [
                'filename' => $attachment->getFilename(),
                'data' => base64_encode($attachment->getBody()),
            ];
        }, $attachments);
    }

    public function __toString(): string
    {
        return 'infobip';
    }
}