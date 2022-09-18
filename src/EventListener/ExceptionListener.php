<?php

namespace App\EventListener;

use App\Exception\AccessException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

/**
 * @property SessionInterface $session
 */
class ExceptionListener
{


    public function __construct(private RouterInterface $router)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $session = $event->getRequest()->getSession();
        // You get the exception object from the received event
        $exception = $event->getThrowable();

        if ($exception instanceof AccessException) {
            $session->getFlashBag()->add('error', $exception->getMessage());
            $event->setResponse(new RedirectResponse($this->router->generate('homepage')));
        } else {
            $session->getFlashBag()->add('error', "Une erreur est survenue !");
            $event->setResponse(new RedirectResponse($this->router->generate('homepage')));
        }
    }
}
