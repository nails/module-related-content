<?php

namespace Nails\RelatedContent;

use Nails\Common\Events\Base;
use Nails\RelatedContent\Event\Listener;

class Events extends Base
{
    public function autoload(): array
    {
        return [
            new Listener\AutoIndex(),
        ];
    }
}
