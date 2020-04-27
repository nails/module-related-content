<?php

use Nails\Common\Model\Base;
use Nails\RelatedContent\Constants;
use Nails\RelatedContent\Factory;
use Nails\RelatedContent\Service;

return [
    'services'  => [
        'Engine' => function (): Service\Engine {

            /** @var Factory\Store $oStore */
            $oStore = \Nails\Factory::factory('Store', Constants::MODULE_SLUG);

            if (class_exists('\App\RelatedContent\Service\Engine')) {
                return new \App\RelatedContent\Service\Engine($oStore);
            } else {
                return new Service\Engine($oStore);
            }
        },
    ],
    'factories' => [
        'Result' => function (string $sType, int $iId, int $iScore, Base $oModel): Factory\Result {
            if (class_exists('\App\RelatedContent\Factory\Result')) {
                return new \App\RelatedContent\Factory\Result($sType, $iId, $iScore, $oModel);
            } else {
                return new Factory\Result($sType, $iId, $iScore, $oModel);
            }
        },
        'Store'  => function (): Factory\Store {
            if (class_exists('\App\RelatedContent\Factory\Store')) {
                return new \App\RelatedContent\Factory\Store();
            } else {
                return new Factory\Store();
            }
        },
    ],
];
