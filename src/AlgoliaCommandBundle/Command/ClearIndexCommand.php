<?php

namespace AlgoliaCommandBundle\Command;

use AlgoliaSearch\AlgoliaException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearIndexCommand extends AbstractAlgoliaCommand
{
    CONST NAME = 'algolia:clear-index';
    CONST ARGUMENT_INDEX = 'index';

    protected function configure()
    {
        parent::configure();

        $this->addArgument(self::ARGUMENT_INDEX, InputArgument::REQUIRED, 'Index name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $indexName = $input->getArgument(self::ARGUMENT_INDEX);

        try {
            $index = $this->client->initIndex($indexName);
            $result = $index->clearIndex($indexName);

            $output->writeln($result);
        } catch (AlgoliaException $e) {
            $output->writeln($e->getMessage());
        }
    }

    public function getName()
    {
        return self::NAME;
    }
}
