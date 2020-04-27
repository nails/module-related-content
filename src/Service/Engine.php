<?php

namespace Nails\RelatedContent\Service;

use HelloPablo\RelatedContent\Factory;
use Nails\Common\Resource;
use Nails\Components;
use Nails\RelatedContent\Analyser;
use Nails\RelatedContent\Factory\Store;

/**
 * Class Engine
 *
 * @package Nails\RelatedContent\Service
 */
class Engine
{
    /** @var Analyser\Base[] */
    protected $aAnalysers = [];

    /** @var string[] */
    protected $aMapping = [];

    /** @var Store */
    protected $oStore;

    /** @var \HelloPablo\RelatedContent\Engine */
    protected $oEngine;

    // --------------------------------------------------------------------------

    /**
     * Engine constructor.
     *
     * @param Store $oStore
     */
    public function __construct(Store $oStore)
    {
        $this->mapAnalysers(
            $this->discoverAnalysers()
        );

        $this->oStore  = $oStore;
        $this->oEngine = Factory::build($this->oStore->getStore());
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of discovered analysers
     *
     * @return array
     */
    protected function discoverAnalysers(): array
    {
        $aAnalysers = [];
        foreach (Components::available() as $oComponent) {

            $oClassCollection = $oComponent
                ->findClasses('RelatedContent\\Analyser')
                ->whichExtend(Analyser\Base::class);

            foreach ($oClassCollection as $sAnalyser) {
                $aAnalysers[] = $sAnalyser;
            }
        }

        return $aAnalysers;
    }

    // --------------------------------------------------------------------------

    /**
     * Creates the analyser maps
     *
     * @param string[] $aAnalysers An array of analyser class names
     *
     * @return $this
     */
    protected function mapAnalysers(array $aAnalysers): self
    {
        foreach ($aAnalysers as $sAnalyser) {

            $oAnalyser = new $sAnalyser();

            $this->aAnalysers[$sAnalyser]                 = $oAnalyser;
            $this->aMapping[$oAnalyser::mapsToResource()] = $sAnalyser;
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Indexes a resource
     *
     * @param Resource $oItem
     *
     * @return $this
     */
    public function index(Resource $oItem): self
    {
        dd('index', $oItem);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Find related content for an item, optionally filtered by type and limit
     *
     * @param Resource $oSource   The source item
     * @param array    $aRestrict The type of content to restrict by
     * @param int      $iLimit    The number of items to return
     *
     * @return array
     */
    public function query(Resource $oSource, array $aRestrict = [], int $iLimit = null): array
    {
        dd('query', $oSource, $aRestrict, $iLimit);
    }
}
