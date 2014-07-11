<?php

namespace AlgoliaCommandBundle\Tests\Command;

use AlgoliaCommandBundle\Command\AbstractAlgoliaCommand;
use AlgoliaCommandBundle\Query\QueryOptions;
use AlgoliaCommandBundle\Command\QueryIndexCommand;
use AlgoliaCommandBundle\Tests\AlgoliaCommandBundleTestCase;
use AlgoliaSearch\Client;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QueryIndexCommandTest extends AlgoliaCommandBundleTestCase
{
    private $apiKey;
    private $applicationId;

    private $output;
    private $client;
    private $container;
    private $command;

    protected function setUp()
    {
        $this->apiKey = 'api-key';
        $this->applicationId = 'application-id';

        $this->client = $this->buildMock('AlgoliaSearch\Client');
        $this->output = $this->buildMock('Symfony\Component\Console\Output\OutputInterface');

        // Setup Container
        $this->container = new Container(
            new ParameterBag(
                array(
                    AbstractAlgoliaCommand::PARAMETER_API_KEY => $this->apiKey,
                    AbstractAlgoliaCommand::PARAMETER_APPLICATION_ID => $this->applicationId
                )
            )
        );

        // Setup Command
        $this->command = new QueryIndexCommandStub(
            $this->apiKey,
            $this->applicationId
        );
        $this->command->setContainer($this->container);
        $this->command->setClient($this->client);
    }

    public function testExecuteWithAllQueryOptions()
    {
        $answer = 'query-index-answer';
        $query = 'search-query';
        $indexName = 'index-name';
        $index = $this->buildMock('AlgoliaSearch\Index');
        $arguments = array(
            QueryIndexCommand::NAME,
            $indexName,
            $query
        );
        $options = array(
            QueryOptions::QUERY_TYPE => 'query-type',
            QueryOptions::TYPO_TOLERANCE => true,
            QueryOptions::MIN_WORD_SIZE_FOR_1_TYPO => 0,
            QueryOptions::MIN_WORD_SIZE_FOR_2_TYPOS => 1,
            QueryOptions::ALLOW_TYPOS_ON_NUMERIC_TOKENS => true,
            QueryOptions::ADVANCED_SYNTAX => true,
            QueryOptions::ANALYTICS => true,
            QueryOptions::SYNONYMS => true,
            QueryOptions::REPLACE_SYNONYMS_IN_HIGHLIGHT => true,
            QueryOptions::OPTIONAL_WORDS => 'optional-words',
            QueryOptions::PAGE => 2,
            QueryOptions::HITS_PER_PAGE => 3,
            QueryOptions::ATTRIBUTES_TO_RETRIEVE => 'attributes-to-retrive',
            QueryOptions::ATTRIBUTES_TO_HIGHLIGHT =>'attributes-to-highlight',
            QueryOptions::ATTRIBUTES_TO_SNIPPET => 'attributes-to-snipper',
            QueryOptions::GET_RANKING_INFO => 4,
            QueryOptions::NUMERIC_FILTERS => 'numeric-filters',
            QueryOptions::TAG_FILTERS => 'tag-filters',
            QueryOptions::DISTINCT => true,
            QueryOptions::FACETS => 'facets',
            QueryOptions::FACET_FILTERS => 'facet-filters',
            QueryOptions::MAX_VALUES_PER_FACET => 5,
            QueryOptions::AROUND_LAT_LNG => 'around-lat-lng',
            QueryOptions::AROUND_RADIUS => 6,
            QueryOptions::AROUND_PRECISION => 7,
            QueryOptions::INSIDE_BOUNDING_BOX => 'inside-bounding-box'
        );
        $input = $this->buildArgvInput(
            $arguments,
            $options
        );

        // Expectations
        $this->client->expects($this->once())
            ->method('initIndex')
            ->with($indexName)
            ->will($this->returnValue($index))
        ;

        $index->expects($this->once())
            ->method('search')
            ->with($query, $options)
            ->will($this->returnValue($answer))
        ;

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($answer)
        ;

        // Run command
        $this->command->run($input, $this->output);
    }

    private function buildArgvInput($arguments = array(), $options = array())
    {
        $argv = array();
        foreach ($arguments as $argument) {
            $argv[] = $argument;
        }
        foreach ($options as $key => $value) {
            $argv[] = '--' . $key .'=' . $value;
        }

        return new ArgvInput($argv);
    }
}

class QueryIndexCommandStub extends QueryIndexCommand
{
    protected function createClient()
    {
        return $this->client;
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    public function callProtectedExecute(InputInterface $input, OutputInterface $output)
    {
        return $this->execute($input, $output);
    }
}
