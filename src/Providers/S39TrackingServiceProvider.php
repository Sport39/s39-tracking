<?php

namespace S39Tracking\Providers;

use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;
use Plenty\Modules\Order\Events\OrderCreated;
use Plenty\Modules\Webshop\Consent\Contracts\ConsentRepositoryContract;
use Plenty\Plugin\ConfigRepository;
use Plenty\Plugin\Events\Dispatcher;
use Plenty\Plugin\ServiceProvider;

/**
 * S39Tracking
 *
 *  - meldet TikTok- und Meta-Pixel als eigene Einwilligungen im Cookie-Banner an
 *  - merkt sich nach einer Bestellung in der Session, dass auf der naechsten
 *    Seite (Bestellbestaetigung) der Kauf gemeldet werden soll
 */
class S39TrackingServiceProvider extends ServiceProvider
{
    public function register()
    {
        /** @var ConsentRepositoryContract $consents */
        $consents = pluginApp(ConsentRepositoryContract::class);

        $consents->registerConsent(
            's39TikTok',
            'S39Tracking::Consent.tiktokLabel',
            function () {
                /** @var ConfigRepository $config */
                $config = pluginApp(ConfigRepository::class);
                return [
                    'description' => 'S39Tracking::Consent.tiktokDescription',
                    'provider'    => 'S39Tracking::Consent.tiktokProvider',
                    'lifespan'    => 'S39Tracking::Consent.tiktokLifespan',
                    'policyUrl'   => 'S39Tracking::Consent.tiktokPolicyUrl',
                    'group'       => $config->get('S39Tracking.consentGroup', 'tracking'),
                    'necessary'   => false,
                    'isOptOut'    => false,
                    'cookieNames' => ['_ttp', '_tt_enable_cookie', 'ttcsid', '/^ttcsid_/']
                ];
            }
        );

        $consents->registerConsent(
            's39Meta',
            'S39Tracking::Consent.metaLabel',
            function () {
                /** @var ConfigRepository $config */
                $config = pluginApp(ConfigRepository::class);
                return [
                    'description' => 'S39Tracking::Consent.metaDescription',
                    'provider'    => 'S39Tracking::Consent.metaProvider',
                    'lifespan'    => 'S39Tracking::Consent.metaLifespan',
                    'policyUrl'   => 'S39Tracking::Consent.metaPolicyUrl',
                    'group'       => $config->get('S39Tracking.consentGroup', 'tracking'),
                    'necessary'   => false,
                    'isOptOut'    => false,
                    'cookieNames' => ['_fbp', '_fbc']
                ];
            }
        );
    }

    public function boot(Dispatcher $dispatcher)
    {
        $dispatcher->listen(OrderCreated::class, function ($event) {
            /** @var FrontendSessionStorageFactoryContract $session */
            $session = pluginApp(FrontendSessionStorageFactoryContract::class);
            $session->getPlugin()->setValue('S39_TRACK_ORDER', 1);
        }, 0);
    }
}
