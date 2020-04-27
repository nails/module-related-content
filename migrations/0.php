<?php

/**
 * Migration:   0
 * Started:     2020-04-27
 */

namespace Nails\Database\Migration\Nails\ModuleRelatedContent;

use Nails\Common\Console\Migrate\Base;

/**
 * Class Migration0
 *
 * @package Nails\Database\Migration\App
 */
class Migration0 extends Base
{
    /**
     * Execute the migration
     */
    public function execute(): void
    {
        $this->query('
                CREATE TABLE IF NOT EXISTS `{{NAILS_DB_PREFIX}}related_content_data` (
                    `hash` varchar(300) CHARACTER SET utf8mb4 DEFAULT NULL,
                    `entity` varchar(150) CHARACTER SET utf8mb4 DEFAULT NULL,
                    `id` varchar(150) CHARACTER SET utf8mb4 DEFAULT NULL,
                    `type` varchar(150) CHARACTER SET utf8mb4 DEFAULT NULL,
                    `value` varchar(150) CHARACTER SET utf8mb4 DEFAULT NULL,
                    KEY `entity` (`entity`,`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ');
    }
}
