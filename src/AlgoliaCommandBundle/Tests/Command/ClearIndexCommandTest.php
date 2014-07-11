<?php

namespace AlgoliaCommandBundle\Tests\Command;

use AlgoliaCommandBundle\Command\AbstractAlgoliaCommand;
use AlgoliaCommandBundle\Command\ClearIndexCommand;
use AlgoliaCommandBundle\Tests\AlgoliaCommandBundleTestCase;
use AlgoliaSearch\Client;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;

class ClearIndexCommandTest extends AlgoliaCommandBundleTestCase
{
    private $output;
    private $client;
    private $command;
    private $container;

    protected function setUp()
    {
        $this->output = $this->buildMock('Symfony\Component\Console\Output\OutputInterface');
        $this->client = $this->buildMock('AlgoliaSearch\Client');

        $this->container = $this->getDefaultContainer($this->apiKey, $this->applicationId);

        $this->command = new ClearIndexCommandStub();
        $this->command->setClient($this->client);
        $this->command->setContainer($this->container);
    }

    public function testExecute()
    {
        $answer = 'clear-index-answer';
        $index = $this->buildMock('AlgoliaSearch\Index');
        $indexName = 'index-name';
        $arguments = array(
            ClearIndexCommand::NAME,
            $indexName
        );
        $input = new ArgvInput($arguments);

        // Expectations
        $this->client->expects($this->once())
            ->method('initIndex')
            ->with($indexName)
            ->will($this->returnValue($index))
        ;

        $index->expects($this->once())
            ->method('clearIndex')
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
     * @expectedException Not enough arguments.
     */
    public function testExecuteWithoutIndexNameThrowsException()
    {
        $input = new ArgvInput(array());

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

class ClearIndexCommandStub extends ClearIndexCommand
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
