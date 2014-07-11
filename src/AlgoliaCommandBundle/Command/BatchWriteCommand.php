<?php

namespace AlgoliaCommandBundle\Command;

use AlgoliaSearch\AlgoliaException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BatchWriteCommand extends AbstractAlgoliaCommand
{
    CONST NAME = 'algolia:batch-write';
    CONST ARGUMENT_INDEX = 'index';
    CONST ARGUMENT_REQUESTS = 'requests';
    CONST ARGUMENT_OBJECT_ID_KEY = 'object-id-key';
    CONST ARGUMENT_OBJECT_ACTION_KEY = 'object-action-key';

    protected function configure()
    {
        parent::configure();

        $this->addArgument(self::ARGUMENT_INDEX, InputArgument::REQUIRED, 'Index name.');
        $this->addArgument(self::ARGUMENT_REQUESTS, InputArgument::REQUIRED, 'Requests JSON.');
        $this->addOption(self::ARGUMENT_OBJECT_ID_KEY, null, InputOption::VALUE_OPTIONAL, 'Object ID Key in Requests JSON array.');
        $this->addOption(self::ARGUMENT_OBJECT_ACTION_KEY, null, InputOption::VALUE_OPTIONAL, 'Object Action Key in Requests JSON array.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $indexName = $input->getArgument(self::ARGUMENT_INDEX);
        $encodedRequests = $input->getArgument(self::ARGUMENT_REQUESTS);
        $objectIDKey = ($input->hasOption(self::ARGUMENT_OBJECT_ID_KEY)) ? $input->getOption(self::ARGUMENT_OBJECT_ID_KEY) : null;
        $objectActionKey = ($input->hasOption(self::ARGUMENT_OBJECT_ACTION_KEY)) ? $input->getOption(self::ARGUMENT_OBJECT_ACTION_KEY) : null;

        // Decode requests argument
        $requests = json_decode($encodedRequests, true);

        if ($requests === null) {
            $output->writeln(AbstractAlgoliaCommand::ERROR_BAD_JSON_MESSAGE);

            return AbstractAlgoliaCommand::STATUS_CODE_ERROR;
        }

        try {
            $index = $this->client->initIndex($indexName);
            $result = $index->batchObjects($requests, $objectIDKey, $objectActionKey);

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
