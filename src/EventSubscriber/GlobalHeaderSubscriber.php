<?php

namespace App\EventSubscriber;

use App\Data\ContentSecurityPolicyInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class GlobalHeaderSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private ContentSecurityPolicyInterface $csp
    )
    {}

    public static function getSubscribedEvents()
    {
        return [
            'kernel.response' => 'addCSPHeaderToResponse', // XSS protection
        ];
    }

    public function addCSPHeaderToResponse(ResponseEvent $event)
    {
        // Do not allow the App to be displayed in an iframe
        $event
            ->getResponse()
            ->headers
            ->set('X-Frame-Options', 'DENY');
        // For HTML responses, set origin policy
        if ($event->getResponse()->headers->contains('content-type', 'text/html')) {
            // @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/script-src
            $this->csp
                ->set('default-src', ["'self'"])
//                ->set('script-src', array(
//                    "'self'",
////                    "'unsafe-inline'",
////                    "'unsafe-eval'",
////                    'strict-dynamic', => requires nonce to work
//                    'https://*.googleapis.com',
//                    'https://*.gstatic.com',
//                    //'https://cdn.tinymce.com',
//                ))
//                ->set('img-src', array("'self'", 'https://*.gstatic.com', 'https://*.googleapis.com'))
//                ->set('style-src', array(
//                    "'self'",
////                    "'unsafe-inline'",
//                    'https://*.googleapis.com',
//                ))
//                ->set('font-src', array(
//                    "'self'",
//                    'https://*.gstatic.com',
//                ))
            ;
        }
        $policy = $this->csp->getPolicy();
        if (!empty($policy)) {
            $event
                ->getResponse()
                ->headers
                ->set('Content-Security-Policy', $policy);
        }
    }
}
