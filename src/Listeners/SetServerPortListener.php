<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Listeners;

use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithHttpRequest;

class SetServerPortListener implements ListenerInterface
{
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
                    $request->server->set('SERVER_PORT', 443);
                } elseif ($request->getScheme() === 'http') {
                    $request->server->set('SERVER_PORT', 80);
                }
            }
        }
    }
}
