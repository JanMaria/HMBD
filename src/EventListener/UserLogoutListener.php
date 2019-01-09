<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Monolog\Logger;
use App\Util\MonologDoctrineHandler;

class UserLogoutListener implements LogoutHandlerInterface
{
    private $logger;

    public function __construct(MonologDoctrineHandler $handler)
    {
        // TODO: to powinno być ustawione w service.yaml... podobnie w UserEventSubscriber
        $this->logger = new Logger('User Events');
        $this->logger->pushHandler($handler);
    }

    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $this->logger->info('Logout success', ['username' => $token->getUser()->getEmail()]);
    }
}
