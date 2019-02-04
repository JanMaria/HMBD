<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Monolog\Logger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\User;
use App\Util\MonologDoctrineHandler;

// use swiftmailer\lib\classes\Swift\Swift_IoException;

class UserEventSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $mailer;
    private $entityManager;
    private $session;

    public function __construct(
        \Swift_Mailer $mailer,
        EntityManagerInterface $entityManager,
        SessionInterface $session,
        MonologDoctrineHandler $handler
    ) {
        $this->logger = new Logger('User Events');
        $this->logger->pushHandler($handler);
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->session = $session;
    }

    public static function getSubscribedEvents()
    {
        return [
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
            SecurityEvents::INTERACTIVE_LOGIN => 'onAuthenticationSuccess'
        ];
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        $lastEmail = $event->getAuthenticationToken()->getUsername();

        $this->logger->warning('Authentication Failure', ['username' => $lastEmail]);

        try {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $lastEmail]);
        } catch (ORMException $exception) {
            $this->session->getFlashBag()->add('dbFailure', 'Błąd obsługi bazy danych');
            goto end;
        }

        if ($user !== null) {
            try {
                $message = (new \Swift_Message())
                ->setSubject('[ytcrud]: Nieudana próba logowania')
                ->setFrom(['ytcrud@gmail.com' => 'ytcrud'])
                ->setTo($lastEmail)
                ->setBody('Ktoś próbował się zalogować na twoje konto przy użyciu niepoprawnego hasła.');

                //TODO: nie wiem czemu wartość wewnątrz if'a zawsze wynosi false,
                // nawet jak zmienię hasło do maila na nieprawidłowe
                if (!$this->mailer->send($message)) {
                    throw new \Swift_SwiftException('Wysyłanie wiadomości nie powiodło się');
                }
                // Wydarzenie logowane do bazy danych,
                // bo informowanie użytkownika nie wydaje się być słusznym rozwiązaniem
            } catch (\Swift_SwiftException $exception) {
                $this->logger->critical('Email Notification Failure', ['username' => $lastEmail]);
            }

            $this->session->getFlashBag()->add('loginFailure', 'Niepoprawne hasło. Wysłano powiadomienie.');
        }
        end:
        return;
    }

    public function onAuthenticationSuccess(InteractiveLoginEvent $event)
    {
        $this->logger
          ->info('Authentication Success', ['username' => $event->getAuthenticationToken()->getUser()->getEmail()]);
    }
}
