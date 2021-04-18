<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\News;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\String\Slugger\AsciiSlugger;

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
        $permittedMethods = [
            Request::METHOD_POST,
            Request::METHOD_PATCH,
            Request::METHOD_PUT
        ];

        if (!$news instanceof News || !in_array($method, $permittedMethods)) {
            // Only handle News entities (Event is called on any Api entity)
            return;
        }

        $title = $news->getTitle();
        if (!$title) {
            return;
        }
        $slugger = new AsciiSlugger();
        $news->setSlug($slugger->slug($title));
    }
}
