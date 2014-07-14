<?php

namespace AlgoliaCommandBundle\Command;

use AlgoliaCommandBundle\Index\IndexSettings;
use AlgoliaSearch\AlgoliaException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ChangeIndexSettingsCommand extends AbstractAlgoliaCommand
{
    CONST NAME = 'algolia:change-index-settings';
    CONST ARGUMENT_INDEX = 'index';

    protected function configure()
    {
        parent::configure();

        $this->addArgument(self::ARGUMENT_INDEX, InputArgument::REQUIRED, 'Index name.');

        foreach (IndexSettings::$all as $setting) {
            $this->addOption($setting, null, InputOption::VALUE_OPTIONAL);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $indexName = $input->getArgument(self::ARGUMENT_INDEX);

        $settings = array();
        foreach (IndexSettings::$all as $setting) {
            if ($input->hasOption($setting)) {
                $settings[$setting] = json_decode(
                    $input->getOption($setting),
                    true
                );
            }
        }

        try {
            $index = $this->client->initIndex($indexName);
            $result = $index->setSettings($settings);

            $output->writeln($result);
        } catch (AlgoliaException $e) {
            $output->writeln($e->getMessage());

            return AbstractAlgoliaCommand::STATUS_CODE_ERROR;
        }
    }

    public function getName()
    {
        return self::NAME;
    }
}
