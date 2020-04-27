<?php

namespace Nails\RelatedContent\Factory;

use HelloPablo\RelatedContent\Exception\MissingExtension;
use HelloPablo\RelatedContent\Store\MySQL;
use Nails\Config;

/**
 * Class Store
 *
 * @package Nails\RelatedContent\Factory
 */
class Store
{
    /** @var MySQL */
    protected $oStore;

    // --------------------------------------------------------------------------

    /**
     * Store constructor.
     *
     * @throws MissingExtension
     */
    public function __construct()
    {
        $this->oStore = new MySQL([
            'host'     => Config::get('DB_HOST'),
            'user'     => Config::get('DB_USERNAME'),
            'pass'     => Config::get('DB_PASSWORD'),
            'database' => Config::get('DB_DATABASE'),
            'port'     => Config::get('DB_PORT'),
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
