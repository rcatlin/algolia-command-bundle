<?php

namespace AlgoliaCommandBundle\Command;

use AlgoliaSearch\AlgoliaException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IndexOperationCommand extends AbstractAlgoliaCommand
{
    CONST NAME = 'algolia:index-operation';
    CONST ARGUMENT_INDEX_SOURCE = 'index-src';
    CONST ARGUMENT_INDEX_DESTINATION = 'index-dest';
    CONST OPTION_MOVE = 'move';
    CONST OPTION_COPY = 'copy';
    CONST CLIENT_METHOD_MOVE = 'moveIndex';
    CONST CLIENT_METHOD_COPY = 'copyIndex';
    CONST ERROR_MESSAGE_BOTH_OPTIONS = 'Cannot both copy and move index.';
    CONST ERROR_MESSAGE_NO_OPTIONS = 'Must provide the "--copy" or "--move" option.';

    protected function configure()
    {
        parent::configure();

        $this->addArgument(self::ARGUMENT_INDEX_SOURCE, InputArgument::REQUIRED, 'Source index name.');
        $this->addArgument(self::ARGUMENT_INDEX_DESTINATION, InputArgument::REQUIRED, 'Destination index destination name.');
        $this->addOption(self::OPTION_MOVE, null, InputOption::VALUE_NONE, 'Move the index.');
        $this->addOption(self::OPTION_COPY, null, InputOption::VALUE_NONE, 'Copy the index.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $move = $input->getOption(self::OPTION_MOVE);
        $copy = $input->getOption(self::OPTION_COPY);

        if ($move && $copy) {
            $output->writeln(self::ERROR_MESSAGE_BOTH_OPTIONS);

            return AbstractAlgoliaCommand::STATUS_CODE_ERROR;
        } elseif (!$move && !$copy) {
            $output->writeln(self::ERROR_MESSAGE_NO_OPTIONS);

            return AbstractAlgoliaCommand::STATUS_CODE_ERROR;
        }

        $srcIndexName = $input->getArgument(self::ARGUMENT_INDEX_SOURCE);
        $destIndexName = $input->getArgument(self::ARGUMENT_INDEX_DESTINATION);

        if ($move) {
            $clientMethod = self::CLIENT_METHOD_MOVE;
        } else {
            $clientMethod = self::CLIENT_METHOD_COPY;
        }

        try {
            $result = $this->client->$clientMethod($srcIndexName, $destIndexName);

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
