<?php

namespace AlgoliaCommandBundle\Command;

use AlgoliaSearch\AlgoliaException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListIndexesCommand extends AbstractAlgoliaCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $output->writeln($this->client->listIndexes());
        } catch (AlgoliaException $e) {
            $output->writeln($e->getMessage());

            return parent::STATUS_CODE_ERROR;
        }
    }

    public function getName()
    {
        return 'algolia:list-indexes';
    }
}
