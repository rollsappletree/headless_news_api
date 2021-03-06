<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\News;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AddAuthorToNewsSubscriber implements EventSubscriberInterface
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['attachOwner', EventPriorities::PRE_WRITE],
        ];
    }

    public function attachOwner(ViewEvent $event)
    {
        $news = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$news instanceof News || $method !== Request::METHOD_POST) {
            // Only handle News entities (Event is called on any Api entity)
            return;
        }
        // maybe these extra null checks are not even needed
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return;
        }

        $owner = $token->getUser();
        if (!$owner instanceof User) {
            return;
        }

        // Attach the user to the not yet persisted News
        $news->setAuthor($owner);
    }
}
