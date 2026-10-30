<?php

namespace Nails\RelatedContent\FindReplace\Exclusion;

use Nails\FindReplace\Interfaces\Exclusion;

class Package implements Exclusion
{
    public function hidden(): array
    {
        return [
            NAILS_DB_PREFIX . 'related_content_data',
        ];
    }

    public function readOnly(): array
    {
        return [];
    }
}
