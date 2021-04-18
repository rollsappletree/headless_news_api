<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\News;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\UnicodeString;

final class AddSlugToNewsSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['addSlug', EventPriorities::PRE_WRITE],
        ];
    }

    public function addSlug(ViewEvent $event)
    {
        $news = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$news instanceof News || Request::METHOD_POST !== $method) {
            // Only handle News entities (Event is called on any Api entity)
            dump('Not a news');
            return;
        }
        dump($news);

        $title = $news->getTitle();
        if (!$title) {
            return;
        }
        $slugger = new AsciiSlugger();
        $news->setSlug($slugger->slug($title));
    }
}
