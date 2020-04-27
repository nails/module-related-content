<?php

namespace Nails\RelatedContent\Service;

use HelloPablo\RelatedContent\Factory;
use HelloPablo\RelatedContent\Query\Hit;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Model\Base;
use Nails\Common\Resource;
use Nails\Common\Service\Event;
use Nails\Components;
use Nails\RelatedContent\Analyser;
use Nails\RelatedContent\Constants;
use Nails\RelatedContent\Exception\IncompatibleObjectException;
use Nails\RelatedContent\Exception\IndexException\ResourceNotFoundException;
use Nails\RelatedContent\Factory\Result;
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
    protected $aModelMap = [];

    /** @var string[] */
    protected $aResourceMap = [];

    /** @var Store */
    protected $oStore;

    /** @var \HelloPablo\RelatedContent\Engine */
    protected $oEngine;

    // --------------------------------------------------------------------------

    /**
     * Engine constructor.
     *
     * @param Store $oStore
     *
     * @throws FactoryException
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

            /** @var Analyser\Base $oAnalyser */
            $oAnalyser = new $sAnalyser();
            $oModel    = $oAnalyser->mapsToModel();
            $sResource = $oAnalyser->mapsToResource();

            $this->aAnalysers[$sAnalyser]        = $oAnalyser;
            $this->aModelMap[get_class($oModel)] = $sAnalyser;
            $this->aResourceMap[$sResource]      = $sAnalyser;
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Automatically sets up indexing listeners
     *
     * @return $this
     * @throws FactoryException
     */
    public function setUpListeners(): self
    {
        /** @var Event $oEventService */
        $oEventService = \Nails\Factory::service('Event');

        foreach ($this->aModelMap as $sModel => $sAnalyser) {

            $sNamespace = $sModel::getEventNamespace();

            $oEventService
                ->subscribe($sModel::EVENT_CREATED, $sNamespace, [$this, 'autoIndex'])
                ->subscribe($sModel::EVENT_UPDATED, $sNamespace, [$this, 'autoIndex'])
                ->subscribe($sModel::EVENT_RESTORED, $sNamespace, [$this, 'autoIndex'])
                ->subscribe($sModel::EVENT_DELETED, $sNamespace, [$this, 'autoDelete']);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the analyser mapped to a model
     *
     * @param Base $oModel The model to test
     *
     * @return Analyser\Base
     * @throws IncompatibleObjectException
     */
    protected function getAnalyserFromModel(Base $oModel): Analyser\Base
    {
        $sClass = get_class($oModel);
        if (!array_key_exists($sClass, $this->aModelMap)) {
            throw new IncompatibleObjectException(
                sprintf(
                    'No analyser registered for model %s',
                    $sClass
                )
            );
        }

        return $this->aAnalysers[$this->aModelMap[$sClass]];
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the analyser mapped to a resource
     *
     * @param Resource\Entity $oResource The resource to test
     *
     * @return Analyser\Base
     * @throws IncompatibleObjectException
     */
    protected function getAnalyserFromResource(Resource\Entity $oResource): Analyser\Base
    {
        $sClass = get_class($oResource);
        if (!array_key_exists($sClass, $this->aResourceMap)) {
            throw new IncompatibleObjectException(
                sprintf(
                    'No analyser registered for resource %s',
                    $sClass
                )
            );
        }

        return $this->aAnalysers[$this->aResourceMap[$sClass]];
    }

    // --------------------------------------------------------------------------

    /**
     * Called by the event listeners when auto-indexing
     *
     * @param int  $iId    The ID of the item
     * @param Base $oModel The model which triggered the event
     *
     * @return $this
     * @throws IncompatibleObjectException
     * @throws ResourceNotFoundException
     * @throws ModelException
     */
    public function autoIndex(int $iId, Base $oModel): self
    {
        $oAnalyser = $this->getAnalyserFromModel($oModel);
        $oResource = $oModel->getById($iId, $oAnalyser->lookupData());

        if (empty($oResource)) {
            throw new ResourceNotFoundException(
                sprintf(
                    'No %s resource found with ID %s',
                    $oAnalyser::mapsToResource(),
                    $iId
                )
            );
        } elseif (!$oResource instanceof Resource\Entity) {
            throw new IncompatibleObjectException(
                sprintf(
                    'Expected %s, got %s',
                    Resource\Entity::class,
                    get_class($oResource)
                )
            );
        }

        $this->index($oResource);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Called by the event listeners when auto-indexing
     *
     * @param int             $iId       The ID of the item
     * @param Resource\Entity $oResource The item which was deleted
     *
     * @throws IncompatibleObjectException
     */
    public function autoDelete(int $iId, Resource\Entity $oResource)
    {
        $this->delete($oResource);
    }

    // --------------------------------------------------------------------------

    /**
     * Indexes a resource
     *
     * @param Resource\Entity $oItem The resource to index
     *
     * @return $this
     * @throws IncompatibleObjectException
     */
    public function index(Resource\Entity $oItem): self
    {
        $oAnalyser = $this->getAnalyserFromResource($oItem);
        $oModel    = $oAnalyser::mapsToModel();

        $this
            ->oEngine
            ->index(
                $oModel->getById(
                    $oAnalyser->getId($oItem),
                    $oAnalyser->lookupData()
                ),
                $oAnalyser
            );

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Deletes indexes for a resource
     *
     * @param Resource\Entity $oItem The resource to delete
     *
     * @return $this
     * @throws IncompatibleObjectException
     */
    public function delete(Resource\Entity $oItem): self
    {
        $this
            ->oEngine
            ->delete(
                $oItem,
                $this->getAnalyserFromResource($oItem)
            );

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Find related content for an item, optionally filtered by type and limit
     *
     * @param Resource\Entity $oSource   The source item
     * @param Analyser\Base[] $aRestrict The type of content to restrict by
     * @param int             $iLimit    The number of items to return
     *
     * @return Result[]
     * @throws IncompatibleObjectException
     * @throws ModelException
     * @throws FactoryException
     */
    public function query(Resource\Entity $oSource, array $aRestrict = [], int $iLimit = null): array
    {
        $aRestrict = array_map(
            function (Analyser\Base $oAnalyser) {
                return get_class($oAnalyser);
            },
            $aRestrict
        );

        $oAnalyser = $this->getAnalyserFromResource($oSource);
        $oModel    = $oAnalyser::mapsToModel();

        /** @var Hit $aHits */
        $aHits = $this
            ->oEngine
            ->query(
                $oModel->getById(
                    $oAnalyser->getId($oSource),
                    $oAnalyser->lookupData()
                ),
                $oAnalyser,
                $aRestrict,
                $iLimit
            );

        $aResults = [];
        foreach ($aHits as $oHit) {

            $oModel     = $oHit->getAnalyser()::mapsToModel();
            $sResource  = $oHit->getAnalyser()::mapsToResource();
            $aResults[] = \Nails\Factory::factory(
                'Result',
                Constants::MODULE_SLUG,
                $sResource,
                $oHit->getId(),
                $oHit->getScore(),
                $oModel
            );
        }

        return $aResults;
    }
}
