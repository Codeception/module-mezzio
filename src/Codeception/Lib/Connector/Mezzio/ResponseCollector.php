<?php

declare(strict_types=1);

namespace Codeception\Lib\Connector\Mezzio;

use LogicException;
use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\Response\EmitterInterface;

class ResponseCollector implements EmitterInterface
{
    /**
     * @var ResponseInterface
     */
    private $response;

    public function emit(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function getResponse(): ResponseInterface
    {
        if ($this->response === null) {
            throw new LogicException('Response wasn\'t emitted yet');
        }
        return $this->response;
    }

    public function clearResponse(): void
    {
        $this->response = null;
    }
}
