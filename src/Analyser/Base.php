<?php

namespace Nails\RelatedContent\Analyser;

use HelloPablo\RelatedContent\Interfaces\Analyser;
use Nails\Common\Resource\Entity;
use Nails\RelatedContent\Exception\IncompatibleObjectException;

abstract class Base implements Analyser
{
    /**
     * Returns an instance of the model this analyser maps to
     *
     * @return \Nails\Common\Model\Base
     */
    abstract public static function mapsToModel(): \Nails\Common\Model\Base;

    // --------------------------------------------------------------------------

    /**
     * Returns the class name of the resource this analyser maps to
     *
     * @return string
     */
    abstract public static function mapsToResource(): string;

    // --------------------------------------------------------------------------

    /**
     * Analyses an item
     *
     * @param object $oItem The item being analysed
     *
     * @return array
     */
    abstract public function analyse(object $oItem): array;

    // --------------------------------------------------------------------------

    /**
     * Returns the resource's ID
     *
     * @param object $oItem The item being analysed
     *
     * @return mixed
     * @throws IncompatibleObjectException
     */
    public function getId(object $oItem)
    {
        if (!$oItem instanceof Entity) {
            throw new IncompatibleObjectException(
                sprintf(
                    'Expected %s, got %s',
                    Entity::class,
                    get_class($oItem)
                )
            );
        }
        return $oItem->id;
    }

    // --------------------------------------------------------------------------

    /**
     * A control array to pass to the model when indexing a resource
     *
     * @return array
     */
    public function lookupData(): array
    {
        return [];
    }
}
