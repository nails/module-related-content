<?php

namespace Nails\RelatedContent\Factory;

use Nails\Common\Exception\ModelException;
use Nails\Common\Model\Base;
use Nails\Common\Resource\Entity;

/**
 * Class Result
 *
 * @package Nails\RelatedContent\Factory
 */
class Result
{
    /** @var string */
    protected $sType;

    /** @var int */
    protected $iId;

    /** @var int */
    protected $iScore;

    /** @var Base */
    protected $oModel;

    // --------------------------------------------------------------------------

    /**
     * Result constructor.
     *
     * @param string $sType  The result's type
     * @param int    $iId    The result's ID
     * @param int    $iScore The result's Score
     * @param Base   $oModel The result's model
     */
    public function __construct(string $sType, int $iId, int $iScore, Base $oModel)
    {
        $this->sType  = $sType;
        $this->iId    = $iId;
        $this->iScore = $iScore;
        $this->oModel = $oModel;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the source object
     *
     * @param array $aData A control array to pass to the model's `getById()` method
     *
     * @return Entity
     * @throws ModelException
     */
    public function getSource(array $aData = []): Entity
    {
        /** @var Entity $oEntity */
        $oEntity = $this->oModel->getById($this->iId, $aData);
        return $oEntity;
    }
}
