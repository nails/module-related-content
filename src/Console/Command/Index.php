<?php

namespace Nails\RelatedContent\Console\Command;

use Nails\Console\Command\Base;
use Nails\RelatedContent\Constants;
use Nails\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Index
 *
 * @package Nails\RelatedContent\Console\Command
 */
class Index extends Base
{
    /**
     * Configure the command
     */
    protected function configure(): void
    {
        $this
            ->setName('relatedcontent:index')
            ->setDescription('Indexes related content')
            ->addOption('fresh', 'f', InputOption::VALUE_NONE, 'Destroys existing relationship data before indexing')
            ->addOption('model', 'm', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Which models to index');
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the command
     *
     * @param InputInterface  $oInput  The Input Interface provided by Symfony
     * @param OutputInterface $oOutput The Output Interface provided by Symfony
     *
     * @return int
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput): int
    {
        parent::execute($oInput, $oOutput);

        try {

            $this->banner('Indexing Related Content');

            /** @var \Nails\RelatedContent\Service\Engine $oEngine */
            $oEngine = Factory::service('Engine', Constants::MODULE_SLUG);

            if ($oInput->getOption('fresh')) {
                $this->warning(['Emptying data store']);
                $oOutput->writeln('');
                $oEngine->empty();
            }

            $aRestrictToModels = $oInput->getOption('model');

            foreach ($oEngine->getModelMap() as $sModel => $sAnalyser) {

                if (!empty($aRestrictToModels) && !in_array($sModel, $aRestrictToModels)) {
                    continue;
                }

                $oOutput->writeln(sprintf(
                    'Indexing <info>%s</info> items:',
                    $sModel
                ));

                /** @var \Nails\Common\Model\Base $oModel */
                $oModel = new $sModel();
                $oQuery = $oModel->getAllRawQuery();

                while ($oItem = $oQuery->unbuffered_row()) {

                    $iItemId = (int) $oItem->{$oModel->getColumnId()};

                    try {

                        $oOutput->write(sprintf(
                            '- Indexing item <comment>#%s</comment>...',
                            $iItemId
                        ));

                        $oEngine->autoIndex(
                            $iItemId,
                            $oModel
                        );

                        $oOutput->writeln(' <comment>done</comment>');

                    } catch (\Throwable $e) {
                        $oOutput->writeln(sprintf(
                            ' <error>%s</error>',
                            $e->getMessage()
                        ));
                    }
                }

                $oOutput->writeln('');
            }

        } catch (\Throwable $e) {
            return $this->abort(
                self::EXIT_CODE_FAILURE,
                [$e->getMessage()]
            );
        }

        return self::EXIT_CODE_SUCCESS;
    }
}
