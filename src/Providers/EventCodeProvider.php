<?php

namespace S39Tracking\Providers;

use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;
use Plenty\Plugin\Templates\Twig;

class EventCodeProvider
{
    public function call(Twig $twig)
    {
        /** @var FrontendSessionStorageFactoryContract $session */
        $session = pluginApp(FrontendSessionStorageFactoryContract::class);

        $trackOrder = $session->getPlugin()->getValue('S39_TRACK_ORDER') === 1;
        if ($trackOrder) {
            // nur einmal melden (Bestellbestaetigung), danach wieder loeschen
            $session->getPlugin()->unsetKey('S39_TRACK_ORDER');
        }

        return $twig->render('S39Tracking::EventCode', [
            'trackOrder' => $trackOrder
        ]);
    }
}
