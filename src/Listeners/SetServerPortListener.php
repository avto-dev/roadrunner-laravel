<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Listeners;

use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithHttpRequest;

class SetServerPortListener implements ListenerInterface
{
    public const
        SERVER_PORT_ATTRIBUTE = 'SERVER_PORT';
    public const
        HTTPS_PORT = 443;
    public const
        HTTP_PORT = 80;

    /**
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        if ($event instanceof WithHttpRequest) {
            $request = $event->httpRequest();

            /** @var int|string|null $port */
            $port = $request->getPort();

            if ($port === null || $port === '') {
                if ($request->getScheme() === 'https') {
                    $request->server->set(static::SERVER_PORT_ATTRIBUTE, static::HTTPS_PORT);
                } elseif ($request->getScheme() === 'http') {
                    $request->server->set(static::SERVER_PORT_ATTRIBUTE, static::HTTP_PORT);
                }
            }
        }
    }
}
