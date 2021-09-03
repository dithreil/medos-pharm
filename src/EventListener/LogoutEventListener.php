<?php

declare(strict_types=1);

namespace App\EventListener;

use App\DataProvider\UserDataProvider;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutEventListener implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param LogoutEvent $event
     */
    public function onLogout(LogoutEvent $event): void
    {
        $token = $event->getToken();
        $user = $token->getUser();

        if (!$user instanceof User) {
            return;
        }

        if ($user->isGranted(UserDataProvider::ROLE_ADMIN)) {
            $path = $this->router->generate('app_admin_security_login');
        } else {
            $path = $this->router->generate('app_front_default_index', ['vueRouting' => 'login/client']);
        }

        $response = new RedirectResponse($path);
        $event->setResponse($response);
    }

    /**
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }
}
