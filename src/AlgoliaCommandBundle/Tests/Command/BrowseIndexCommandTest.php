<?php

namespace AlgoliaCommandBundle\Tests\Command;

use AlgoliaCommandBundle\Command\AbstractAlgoliaCommand;
use AlgoliaCommandBundle\Command\BrowseIndexCommand;
use AlgoliaCommandBundle\Tests\AlgoliaCommandBundleTestCase;
use AlgoliaSearch\AlgoliaException;
use AlgoliaSearch\Client;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;

class BrowseIndexCommandTest extends AlgoliaCommandBundleTestCase
{
    protected function setUp()
    {
        $this->output = $this->buildMock('Symfony\Component\Console\Output\OutputInterface');
        $this->client = $this->buildMock('AlgoliaSearch\Client');

        $this->container = $this->getDefaultContainer($this->apiKey, $this->applicationId);

        $this->command = new BrowseIndexCommandStub();
        $this->command->setClient($this->client);
        $this->command->setContainer($this->container);
    }

    public function testExecute()
    {
        $answer = 'answer';
        $indexName = 'index-name';
        $page = 3;
        $hitsPerPage = 55;
        $input = new ArgvInput(
            array(
                BrowseIndexCommand::NAME,
                $indexName,
                '--' . BrowseIndexCommand::OPTION_PAGE . '=' . $page,
                '--' . BrowseIndexCommand::OPTION_HITS_PER_PAGE . '=' . $hitsPerPage
            )
        );
        $index = $this->buildMock('AlgoliaSearch\Index');

        // Expectations
        $this->client->expects($this->once())
            ->method('initIndex')
            ->with($indexName)
            ->will($this->returnValue($index))
        ;

        $index->expects($this->once())
            ->method('browse')
            ->with($page, $hitsPerPage)
            ->will($this->returnValue($answer))
        ;

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($answer)
        ;

        // Run command
        $result = $this->command->run(
            $input,
            $this->output
        );

        // Assertions
        $this->assertEquals(
            $result,
            null
        );
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Not enough arguments.
     */
    public function testExecuteWithoutIndexNameThrowsException()
    {
        $input = new ArgvInput(
            array(
                BrowseIndexCommand::NAME,
                '--' . BrowseIndexCommand::OPTION_PAGE . '=0',
                '--' . BrowseIndexCommand::OPTION_HITS_PER_PAGE . '=20'
            )
        );

        // Run command
        $this->command->run(
            $input,
            $this->output
        );

        // Assertions
        $this->assertEquals(
            $result,
            AbstractAlgoliaCommand::STATUS_CODE_ERROR
        );
    }

    public function testExecuteIndexThrowsException()
    {
        $indexName = 'index-name';
        $exceptionMessage = 'exception-message';

        $input = new ArgvInput(
            array(
                BrowseIndexCommand::NAME,
                $indexName,
            )
        );
        $index = $this->buildMock('AlgoliaSearch\Index');

        // Expectations
        $this->client->expects($this->once())
            ->method('initIndex')
            ->with($indexName)
            ->will($this->returnValue($index))
        ;

        $index->expects($this->once())
            ->method('browse')
            ->will($this->throwException(new AlgoliaException($exceptionMessage)))
        ;

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($exceptionMessage)
        ;

        // Run command
        $result = $this->command->run(
            $input,
            $this->output
        );

        // Assertions
        $this->assertEquals(
            $result,
            AbstractAlgoliaCommand::STATUS_CODE_ERROR
        );
    }
}

class BrowseIndexCommandStub extends BrowseIndexCommand
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
