<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\TextResponse;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        $action = $request->getAttribute('action');
        if ('set' === $action) {
            $session->set('name', 'Somebody');

            return new TextResponse('Name set');
        }

        if ('get' === $action) {
            $name = $session->get('name', 'Nobody');

            return new TextResponse(sprintf('The name is: %s', $name));
        }

        return new EmptyResponse();
    }
}
