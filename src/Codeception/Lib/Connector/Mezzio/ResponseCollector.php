<?php

declare(strict_types=1);

namespace Codeception\Lib\Connector\Mezzio;

use Laminas\Diactoros\Response\EmitterInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;

class ResponseCollector implements EmitterInterface
{
    private ?ResponseInterface $response = null;

    public function emit(ResponseInterface $response): void
    {
        $this->response = $response;
    }

    public function getResponse(): ResponseInterface
    {
        if ($this->response === null) {
            throw new LogicException("Response wasn't emitted yet");
        }

        return $this->response;
    }

    public function clearResponse(): void
    {
        $this->response = null;
    }
}
