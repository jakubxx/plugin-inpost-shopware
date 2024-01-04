<?php

namespace WebLivesInPost\Models\Payload;

use Psr\Http\Message\ResponseInterface;

class ShipXResponse
{
    public const STATUS_OK = 200;
    public const STATUS_CREATED = 201;
    public const STATUS_BAD_REQUEST = 400;
    public const STATUS_UNAUTHORIZED = 401;
    public const STATUS_NOT_FOUND = 404;

    /**
     * @var ResponseInterface
     */
    private $original;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var array
     */
    private $body;

    /**
     * ShipXResponse constructor.
     * @param ResponseInterface $original
     */
    public function __construct(ResponseInterface $original)
    {
        $this->original = $original;
        $this->statusCode = $this->original->getStatusCode();
        $this->body = json_decode($this->original->getBody()->getContents(), true);
    }

    /**
     * @return ResponseInterface
     */
    public function getOriginal(): ResponseInterface
    {
        return $this->original;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }
}
