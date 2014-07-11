<?php

namespace AlgoliaCommandBundle\Command;

use AlgoliaSearch\AlgoliaException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BrowseIndexCommand extends AbstractAlgoliaCommand
{
    CONST NAME = 'algolia:browse-index';
    CONST ARGUMENT_INDEX = 'index';
    CONST OPTION_PAGE = 'page';
    CONST OPTION_HITS_PER_PAGE = 'hits-per-page';

    protected function configure()
    {
        parent::configure();

        $this->addArgument(self::ARGUMENT_INDEX, InputArgument::REQUIRED, 'Index name.');
        $this->addOption(self::OPTION_PAGE, null, InputOption::VALUE_OPTIONAL, 'Page number (zero-based).');
        $this->addOption(self::OPTION_HITS_PER_PAGE, null, InputOption::VALUE_OPTIONAL, 'Hits per page.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $indexName = $input->getArgument(self::ARGUMENT_INDEX);
        $page = ($input->hasOption(self::OPTION_PAGE)) ? $input->getOption(self::OPTION_PAGE) : null;
        $hitsPerPage = ($input->hasOption(self::OPTION_HITS_PER_PAGE)) ? $input->getOption(self::OPTION_HITS_PER_PAGE) : null;

        try {
            $index = $this->client->initIndex($indexName);
            $result = $index->browse($page, $hitsPerPage);

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
