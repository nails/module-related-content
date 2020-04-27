<?php

namespace Nails\RelatedContent\Service;

/**
 * Class Engine
 *
 * @package Nails\RelatedContent\Service
 */
class Engine
{
    /** @var \Nails\RelatedContent\Factory\Store */
    protected $oStore;

    // --------------------------------------------------------------------------

    /**
     * Engine constructor.
     *
     * @param \Nails\RelatedContent\Factory\Store $oStore
     */
    public function __construct(\Nails\RelatedContent\Factory\Store $oStore)
    {
        $this->oStore = $oStore;
    }
}
