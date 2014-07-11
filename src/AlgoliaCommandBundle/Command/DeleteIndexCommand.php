<?php

namespace AlgoliaCommandBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteIndexCommand extends AbstractAlgoliaCommand
{
    CONST NAME = 'algolia:delete-index';
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
            $result = $client = $this->client->deleteIndex($indexName);

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
