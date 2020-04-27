<?php

namespace Nails\RelatedContent\Analyser;

abstract class Base
{
    /**
     * Returns the resource type this analyser maps to
     *
     * @return string
     */
    abstract public static function mapsToResource(): string;
}
