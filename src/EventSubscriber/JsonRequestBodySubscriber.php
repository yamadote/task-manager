<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class JsonRequestBodySubscriber implements EventSubscriberInterface
{
    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $request->request->replace($this->getRequestContentData($request));
        }
    }

    private function getRequestContentData(Request $request): array
    {
        $content = $request->getContent();
        if (empty($content)) {
            return [];
        }
        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        return is_array($data) ? $data : [];
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController'
        ];
    }
}
