<?php

namespace AlgoliaCommandBundle\Tests\Command;

use AlgoliaSearch\Client;
use AlgoliaCommandBundle\Command\ChangeIndexSettingsCommand;
use AlgoliaCommandBundle\Index\IndexSettings;
use AlgoliaCommandBundle\Tests\AlgoliaCommandBundleTestCase;
use Symfony\Component\Console\Input\ArgvInput;

class ChangeIndexSettingsCommandTest extends AlgoliaCommandBundleTestCase
{
    private $output;
    private $client;
    private $container;
    private $command;

    protected function setUp()
    {
        $this->output = $this->buildMock('Symfony\Component\Console\Output\OutputInterface');
        $this->client = $this->buildMock('AlgoliaSearch\Client');

        $this->container = $this->getDefaultContainer($this->apiKey, $this->applicationId);

        $this->command = new ChangeIndexSettingsCommandStub();
        $this->command->setClient($this->client);
        $this->command->setContainer($this->container);
    }

    public function testExecute()
    {
        $settings = array(
            IndexSettings::ATTRIBUTES_TO_INDEX => array(
                'index-attribute-0',
                'index-attribute-1',
            ),
            IndexSettings::ATTRIBUTES_FOR_FACETING  => array(
                'facet-attribute-0',
                'facet-attribute-1',
            ),
            IndexSettings::ATTRIBUTE_FOR_DISTINCT  => 'distinct-attribute',
            IndexSettings::RANKING  => array(
                'rank-0',
                'rank-1',
                'rank-2',
            ),
            IndexSettings::CUSTOM_RANKING  => array(
                'rank-2',
                'rank-0',
                'rank-1',
            ),
            IndexSettings::SEPARATORS_TO_INDEX => '::',
            IndexSettings::SLAVES => array(
                'slave-0',
                'slave-1',
                'slave-2',
            ),
            IndexSettings::SYNONYMS => array(
                array('synonym-0a' => 'synonym-0b'),
                array('synonym-1a' => 'synonym-1b'),
                array('synonym-2a' => 'synonym-2b'),
            ),
            IndexSettings::PLACEHOLDERS => array(
                'placeholder-0' => array(
                    'placeholder-value-0a',
                    'placeholder-value-0b',
                ),
                'placeholder-1' => array(
                    'placeholder-value-1a',
                    'placeholder-value-1b',
                    'placeholder-value-1c',
                )
            ),
            IndexSettings::DISABLE_TYPO_TOLERANCE_ON => array(
                'disabled-typo-0',
                'disabled-typo-1'
            ),
            IndexSettings::ALT_CORRECTIONS => array(
                array(
                    'word' => 'word-0',
                    'correction' => 'alt-word-0',
                    'nbTypos' => 1,
                ),
                array(
                    'word' => 'word-1',
                    'correction' => 'alt-word-1',
                    'nbTypos' => 2
                )
            ),
            IndexSettings::MIN_WORD_SIZE_FOR_1_TYPO => 3,
            IndexSettings::MIN_WORD_SIZE_FOR_2_TYPOS => 4,
            IndexSettings::HITS_PER_PAGE => 50,
            IndexSettings::ATTRIBUTES_TO_RETRIEVE => array(
                'retrieve-attribute-0',
                'retrieve-attribute-1',
            ),
            IndexSettings::ATTRIBUTES_TO_HIGHLIGHT => array(
                'highlight-attribute-0',
                'highlight-attribute-1',
            ),
            IndexSettings::ATTRIBUTES_TO_SNIPPET => array(
                'snippet-attribute-0',
                'snippet-attribute-1',
            ),
            IndexSettings::QUERY_TYPE => 'query-type',
            IndexSettings::HIGHLIGHT_PRE_TAG => 'highlight-pre-tag',
            IndexSettings::HIGHLIGHT_POST_TAG => 'highlight-post-tag',
            IndexSettings::OPTIONAL_WORDS => array(
                'optional-word-0',
                'optional-word-1',
                'optional-word-2',
            ),
        );
        $indexName = 'index-name';
        $answer = 'index-result';
        $index = $this->buildMock('AlgoliaSearch\Index');

        // Setup Input
        $argv = array(
            ChangeIndexSettingsCommand::NAME,
            $indexName
        );
        foreach ($settings as $key => $value) {
            $argv[] = '--' . $key . '=' . json_encode($value);
        }
        $input = new ArgvInput(
            $argv
        );

        // Expectations
        $this->client->expects($this->once())
            ->method('initIndex')
            ->with($indexName)
            ->will($this->returnValue($index))
        ;

        $index->expects($this->once())
            ->method('setSettings')
            ->with($settings)
            ->will($this->returnValue($answer))
        ;

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($answer)
        ;

        // Run command
        $result = $this->command->run($input, $this->output);

        // Assertions
        $this->assertEquals(
            $result,
            null
        );
    }
}

class ChangeIndexSettingsCommandStub extends ChangeIndexSettingsCommand
{
    protected function createClient()
    {
        return $this->client;
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
    }
}
