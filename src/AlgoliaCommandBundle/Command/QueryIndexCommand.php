<?php

namespace AlgoliaCommandBundle\Command;

use AlgoliaCommandBundle\Query\QueryOptions;
use AlgoliaSearch\AlgoliaException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class QueryIndexCommand extends AbstractAlgoliaCommand
{
    CONST NAME = 'algolia:query-index';
    CONST ARGUMENT_INDEX = 'index';
    CONST ARGUMENT_QUERY = 'query';

    protected function configure()
    {
        parent::configure();

        $definition = new InputDefinition();

        $this->addArgument(self::ARGUMENT_INDEX, InputArgument::REQUIRED, 'Index name.');
        $this->addArgument(self::ARGUMENT_QUERY, InputArgument::OPTIONAL, 'Query string.');

        foreach (QueryOptions::$all as $option) {
            $this->addOption($option, null, InputOption::VALUE_OPTIONAL);
        }
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $indexName = $input->getArgument(self::ARGUMENT_INDEX);
        $query = $input->getArgument(self::ARGUMENT_QUERY);

        $options = array();
        foreach (QueryOptions::$all as $option) {
            if ($input->hasOption($option)) {
                $options[$option] =
                QueryOptions::evaluate(
                    $option,
                    $input->getOption($option)
                );
            }
        }

        try {
            $index = $this->client->initIndex($indexName);
            $result = $index->search($query, $options);

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
