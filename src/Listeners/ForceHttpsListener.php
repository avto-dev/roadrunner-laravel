<?php

declare(strict_types = 1);

namespace AvtoDev\RoadRunnerLaravel\Listeners;

use Illuminate\Routing\UrlGenerator;
use AvtoDev\RoadRunnerLaravel\ServiceProvider;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithHttpRequest;
use AvtoDev\RoadRunnerLaravel\Events\Contracts\WithApplication;

class ForceHttpsListener implements ListenerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($event): void
    {
        if ($event instanceof WithHttpRequest && $event instanceof WithApplication) {
            $app = $event->application();

            /** @var ConfigRepository $config */
            $config = $app->make(ConfigRepository::class);

            if ((bool) $config->get(ServiceProvider::getConfigRootKey() . '.force_https', false)) {
                /** @var UrlGenerator $url_generator */
                $url_generator = $app->make(UrlGenerator::class);

                $url_generator->forceScheme('https');

                // Set 'HTTPS' server parameter (required for correct working request methods like ::isSecure and others)
                $event->httpRequest()->server->set('HTTPS', 'on');
            }
        }
    }
}
