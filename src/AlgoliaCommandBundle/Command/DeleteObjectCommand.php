<?php

namespace AlgoliaCommandBundle\Command;

use AlgoliaSearch\AlgoliaException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteObjectCommand extends AbstractAlgoliaCommand
{
    CONST NAME = 'algolia:delete-object';
    CONST ARGUMENT_INDEX = 'index';
    CONST ARGUMENT_ID = 'id';

    protected function configure()
    {
        parent::configure();

        $this->addArgument(self::ARGUMENT_INDEX, InputArgument::REQUIRED, 'Index name.');
        $this->addArgument(self::ARGUMENT_ID, InputArgument::REQUIRED, 'Object ID.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $indexName = $input->getArgument(self::ARGUMENT_INDEX);
        $id = $input->getArgument(self::ARGUMENT_ID);

        try {
            $index = $this->client->initIndex($indexName);
            $result = $index->deleteObject($id);

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
