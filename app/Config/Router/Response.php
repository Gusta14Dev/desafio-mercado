<?php

declare(strict_types=1);

namespace App\Config\Router;

class Response
{
    private string $content = '';
    private string $contentType = "Aplication/json";
    private array $headers = [];

    public function __construct(string|array|object $content = [], string $status = "success", private int $httpCode = 200,  string $contentType = "Aplication/json")
    {
        $this->setContent($content, $status);
        $this->setContentType($contentType);
    }

    public function setContent(string|array|object $content, string $status = "success"): string
    {
        if (is_string($content))
            return $this->content = $content;

        switch ($status) {
            case 'success':
                $content = [
                    'status' => $status,
                    'body' => $content
                ];
                break;

            case 'error':
                $content = [
                    'status' => $status,
                    'errors' => $content
                ];
                break;

            default:
                $content = [
                    'status' => $status,
                    'body' => $content
                ];
                break;
        }


        return $this->content = json_encode($content, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function setContentType(string $contentType): string
    {
        $this->contentType = $contentType;
        $this->addHeader('Content-Type', $this->contentType);

        return $this->contentType;
    }

    public function addHeader(string $key, string $value): array
    {
        $this->headers[$key] = $value;

        return $this->headers;
    }

    private function sendHeaders(): void
    {
        http_response_code($this->httpCode);
        foreach ($this->headers as $key => $value) {
            header($key . ": " . $value);
        }
    }

    public function send(): void
    {
        $this->sendHeaders();

        switch ($this->contentType) {
            case 'text/html':
                echo $this->content;
                exit;

            case 'Aplication/json':
                echo $this->content;
                exit;

            default:
                echo $this->content;
                break;
        }
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
