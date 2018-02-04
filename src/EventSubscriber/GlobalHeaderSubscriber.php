<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 */
class GlobalHeaderSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            'kernel.response' => 'onKernelResponse'
        ];
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        // Do not allow the App to be displayed in an iframe
        $event
            ->getResponse()
            ->headers
            ->set('X-Frame-Options', 'SAMEORIGIN');
        // for HTML responses
        $fullPolicy = '';
        // Set origin policy
        $allowedContentOrigins = array(
            'default-src' => array(
                "'self'"
            )
        );
        if ($event->getResponse()->headers->contains('content-type', 'text/html')) {
            $allowedContentOrigins += array(
                'script-src' => array(
                    "'self'",
                    "'unsafe-inline'",
                    "'unsafe-eval'",
                    'https://*.googleapis.com',
                    'https://*.gstatic.com',
                //'https://cdn.tinymce.com',
                ),
                'img-src' => array(
                    "'self'",
                    'https://*.gstatic.com',
                    'https://*.googleapis.com'
                ),
                'style-src' => array(
                    "'self'",
                    "'unsafe-inline'",
                    'https://*.googleapis.com',
                ),
                'font-src' => array(
                    "'self'",
                    'https://*.gstatic.com',
                )
                //
            );
        }
        foreach ($allowedContentOrigins as $srcType => $policy) {
            $fullPolicy .= $srcType.' '.implode(' ', $policy).'; ';
        }
        $event
            ->getResponse()
            ->headers
            ->set('Content-Security-Policy', $fullPolicy);
    }
}
