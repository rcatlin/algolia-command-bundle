<?php

namespace AlgoliaCommandBundle\Command;

use AlgoliaSearch\AlgoliaException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CopyIndexCommand extends AbstractAlgoliaCommand
{
    CONST NAME = 'algolia:copy-index';
    CONST ARGUMENT_SOURCE = 'source';
    CONST ARGUMENT_DESTINATION = 'destination';

    protected function configure()
    {
        parent::configure();

        $this->addArgument(self::ARGUMENT_SOURCE, InputArgument::REQUIRED, 'Source Index name to copy.');
        $this->addArgument(self::ARGUMENT_DESTINATION, InputArgument::REQUIRED, 'Copy Index name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument(self::ARGUMENT_SOURCE);
        $destination = $input->getArgument(self::ARGUMENT_DESTINATION);

        try {
            $result = $this->client->copyIndex($source, $destination);

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
