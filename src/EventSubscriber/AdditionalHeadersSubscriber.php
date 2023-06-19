<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use const JSON_THROW_ON_ERROR;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

final class AdditionalHeadersSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => ['addSecurityHeaders'],
        ];
    }

    public function addSecurityHeaders(ResponseEvent $event): void
    {
        $event->getResponse()
            ->headers->set(
                'permissions-policy',
                'accelerometer=(self), autoplay=(), camera=(self), encrypted-media=(), fullscreen=(self), geolocation=(self), gyroscope=(self), magnetometer=(self), microphone=(), midi=(), payment=(), picture-in-picture=(), sync-xhr=(self), usb=()'
            );
        $event->getResponse()
            ->headers->set('Report-To', json_encode([
                'group' => 'default',
                'max_age' => 31_536_000,
                'endpoints' => [
                    [
                        'url' => 'https://5d93a5635ffa8890a438a991da67f78d.report-uri.com/a/d/g',
                    ],
                ],
                'include_subdomains' => true,
            ], JSON_THROW_ON_ERROR));
        $event->getResponse()
            ->headers->set('nel', json_encode([
                'report_to' => 'default',
                'max_age' => 31_536_000,
                'include_subdomains' => true,
            ], JSON_THROW_ON_ERROR));
        $event->getResponse()
            ->headers->set('Cross-Origin-Embedder-Policy', 'unsafe-none; report-to="default"'); //require-corp
        $event->getResponse()
            ->headers->set('Cross-Origin-Opener-Policy', 'same-origin; report-to="default"');
        $event->getResponse()
            ->headers->set('Cross-Origin-Resource-Policy', 'same-origin; report-to="default"');
    }
}
