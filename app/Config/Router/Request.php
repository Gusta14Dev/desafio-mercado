<?php

declare(strict_types=1);

namespace App\Config\Router;

class Request
{

    private string $httpMethod;

    private string $uri;

    private array $queryParams;

    private array $postParams;

    private array $headers = [];

    public function __construct()
    {
        $this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->uri = $_SERVER['REQUEST_URI'] ?? '';
        $this->queryParams = $_GET ?? [];
        $this->postParams = $_POST ?? [];

        if($this->httpMethod == 'PUT')
            $this->postParams = $this->parsePutRequest();

        $this->headers = getallheaders();
    }

    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function getPostParams(): array
    {
        return $this->postParams;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function all(): array
    {
        return array_merge($this->getPostParams(), $this->getQueryParams());
    }

    function parsePutRequest()
    {
        // Fetch content and determine boundary
        $raw_data = file_get_contents('php://input');
        $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

        // Fetch each part
        $parts = array_slice(explode($boundary, $raw_data), 1);
        $data = array();

        foreach ($parts as $part) {
            // If this is the last part, break
            if ($part == "--\r\n") break;

            // Separate content from headers
            $part = ltrim($part, "\r\n");
            list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

            // Parse the headers list
            $raw_headers = explode("\r\n", $raw_headers);
            $headers = array();
            foreach ($raw_headers as $header) {
                list($name, $value) = explode(':', $header);
                $headers[strtolower($name)] = ltrim($value, ' ');
            }

            // Parse the Content-Disposition to get the field name, etc.
            if (isset($headers['content-disposition'])) {
                $filename = null;
                preg_match(
                    '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                    $headers['content-disposition'],
                    $matches
                );
                list(, $type, $name) = $matches;
                isset($matches[4]) and $filename = $matches[4];

                // handle your fields here
                switch ($name) {
                        // this is a file upload
                    case 'userfile':
                        file_put_contents($filename, $body);
                        break;

                        // default for all other files is to populate $data
                    default:
                        $data[$name] = substr($body, 0, strlen($body) - 2);
                        break;
                }
            }
        }
        return $data;
    }
}
