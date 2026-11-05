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
 *  - meldet TikTok- und Meta-Pixel, Google (Tag Manager) und billiger.de/solute
 *    als eigene Einwilligungen im Cookie-Banner an
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

        /** @var ConfigRepository $cfg */
        $cfg = pluginApp(ConfigRepository::class);

        // Google (Tag Manager mit Analytics 4 und Google Ads) – nur wenn eine Container-ID eingetragen ist
        if (trim((string)$cfg->get('S39Tracking.gtmContainerId', '')) !== '') {
            $consents->registerConsent(
                's39Google',
                'S39Tracking::Consent.googleLabel',
                function () {
                    /** @var ConfigRepository $config */
                    $config = pluginApp(ConfigRepository::class);
                    return [
                        'description' => 'S39Tracking::Consent.googleDescription',
                        'provider'    => 'S39Tracking::Consent.googleProvider',
                        'lifespan'    => 'S39Tracking::Consent.googleLifespan',
                        'policyUrl'   => 'S39Tracking::Consent.googlePolicyUrl',
                        'group'       => $config->get('S39Tracking.consentGroup', 'tracking'),
                        'necessary'   => false,
                        'isOptOut'    => false,
                        'cookieNames' => ['_ga', '/^_ga_/', '_gcl_au', '/^_gcl_/', '_gid']
                    ];
                }
            );
        }

        // billiger.de / solute – merkt sich die Klick-ID im Browser (localStorage, keine Cookies)
        if ((string)$cfg->get('S39Tracking.soluteEnabled', '1') === '1') {
            $consents->registerConsent(
                's39Solute',
                'S39Tracking::Consent.soluteLabel',
                function () {
                    /** @var ConfigRepository $config */
                    $config = pluginApp(ConfigRepository::class);
                    return [
                        'description' => 'S39Tracking::Consent.soluteDescription',
                        'provider'    => 'S39Tracking::Consent.soluteProvider',
                        'lifespan'    => 'S39Tracking::Consent.soluteLifespan',
                        'policyUrl'   => 'S39Tracking::Consent.solutePolicyUrl',
                        'group'       => $config->get('S39Tracking.consentGroup', 'tracking'),
                        'necessary'   => false,
                        'isOptOut'    => false,
                        'cookieNames' => []
                    ];
                }
            );
        }
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
