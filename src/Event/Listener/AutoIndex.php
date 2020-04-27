<?php

namespace Nails\RelatedContent\Event\Listener;

use Nails\Common\Events;
use Nails\Common\Events\Subscription;
use Nails\Common\Exception\FactoryException;
use Nails\Config;
use Nails\Factory;
use Nails\RelatedContent\Constants;
use Nails\RelatedContent\Service\Engine;

/**
 * Class AutoIndex
 *
 * @package Nails\RelatedContent\Event\Listener
 */
class AutoIndex extends Subscription
{
    /**
     * AutoIndex constructor.
     */
    public function __construct()
    {
        $this
            ->setEvent(Events::SYSTEM_STARTUP)
            ->setCallback([$this, 'execute']);
    }

    // --------------------------------------------------------------------------

    /**
     * @throws FactoryException
     */
    public function execute(): void
    {
        if (Config::get('RELATED_CONTENT_AUTO_INDEX', true)) {
            /** @var Engine $oEngine */
            $oEngine = Factory::service('Engine', Constants::MODULE_SLUG);
            $oEngine->setUpListeners();
        }
    }
}
