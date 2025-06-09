<?php

namespace Nails\RelatedContent\Factory;

use HelloPablo\RelatedContent\Exception\MissingExtension;
use HelloPablo\RelatedContent\Store\MySQL;
use Nails\Common\Service\Database;
use Nails\Config;
use Nails\Factory;
use Nails\Testing;

/**
 * Class Store
 *
 * @package Nails\RelatedContent\Factory
 */
class Store
{
    /** @var MySQL */
    protected MySQL $oStore;

    // --------------------------------------------------------------------------

    /**
     * Store constructor.
     *
     * @throws MissingExtension
     */
    public function __construct()
    {
        /** @var Database */
        $oDb = Factory::service('Database');

        $this->oStore = new MySQL([
            'host'     => $oDb->getHost(),
            'user'     => $oDb->getUsername(),
            'pass'     => $oDb->getPassword(),
            'database' => $oDb->getDatabase(),
            'port'     => $oDb->getPort(),
            'table'    => NAILS_DB_PREFIX . 'related_content_data',
        ]);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the instance of the store
     *
     * @return \HelloPablo\RelatedContent\Interfaces\Store
     */
    public function getStore(): \HelloPablo\RelatedContent\Interfaces\Store
    {
        return $this->oStore;
    }
}
