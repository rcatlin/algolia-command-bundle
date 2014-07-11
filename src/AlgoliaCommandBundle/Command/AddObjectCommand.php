<?php

namespace AlgoliaCommandBundle\Command;

use AlgoliaSearch\AlgoliaException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AddObjectCommand extends AbstractAlgoliaCommand
{
    CONST NAME = 'algolia:add-object';
    CONST ARGUMENT_INDEX = 'index';
    CONST ARGUMENT_CONTENT = 'content';
    CONST OPTION_ID = 'id';

    protected function configure()
    {
        parent::configure();

        $this->addArgument(self::ARGUMENT_INDEX, InputArgument::REQUIRED, 'Index name.');
        $this->addArgument(self::ARGUMENT_CONTENT, InputArgument::REQUIRED, 'Object content.');
        $this->addOption(self::OPTION_ID, self::OPTION_ID, InputOption::VALUE_OPTIONAL, 'Object ID.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $indexName = $input->getArgument(self::ARGUMENT_INDEX);
        $content = $input->getArgument(self::ARGUMENT_CONTENT);
        $id = ($input->hasOption(self::OPTION_ID)) ? $input->getOption(self::OPTION_ID) : null;

        $content = json_decode($content, true);

        if (null === $content) {
            $output->writeln(AbstractAlgoliaCommand::ERROR_BAD_JSON_MESSAGE);

            return AbstractAlgoliaCommand::STATUS_CODE_ERROR;
        }

        try {
            $index = $this->client->initIndex($indexName);
            $result = $index->addObject($content, $id);

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
