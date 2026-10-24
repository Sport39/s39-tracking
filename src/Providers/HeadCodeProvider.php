<?php

namespace S39Tracking\Providers;

use Plenty\Plugin\Templates\Twig;

class HeadCodeProvider
{
    public function call(Twig $twig)
    {
        return $twig->render('S39Tracking::HeadCode');
    }
}
