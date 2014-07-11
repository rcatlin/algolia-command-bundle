<?php

namespace AlgoliaCommandBundle\Tests\Command;

use AlgoliaCommandBundle\Command\AbstractAlgoliaCommand;
use AlgoliaCommandBundle\Command\ListIndexesCommand;
use AlgoliaCommandBundle\Tests\AlgoliaCommandBundleTestCase;
use AlgoliaSearch\AlgoliaException;
use AlgoliaSearch\Client;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListIndexesCommandTest extends AlgoliaCommandBundleTestCase
{
    private $input;
    private $output;
    private $client;
    private $command;
    private $container;

    protected function setUp()
    {
        $this->input = $this->buildMock('Symfony\Component\Console\Input\InputInterface');
        $this->output = $this->buildMock('Symfony\Component\Console\Output\OutputInterface');
        $this->client = $this->buildMock('AlgoliaSearch\Client');

        $this->container = $this->getDefaultContainer($this->apiKey, $this->applicationId);

        $this->command = new ListIndexesCommandStub();
        $this->command->setClient($this->client);
        $this->command->setContainer($this->container);
    }

    public function testExecute()
    {
        $answer = 'list-indexes-answer';

        // Expectations
        $this->client->expects($this->once())
            ->method('listIndexes')
            ->will($this->returnValue($answer))
        ;

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($answer)
        ;

        // Run command
        $result = $this->command->run(
            $this->input,
            $this->output
        );

        // Assertions
        $this->assertEquals(
            $result,
            null
        );
    }

    public function testExecuteThrowsException()
    {
        $message ='algolia-exception-message';
        $exception = new AlgoliaException($message);

        // Expectations
        $this->client->expects($this->once())
            ->method('listIndexes')
            ->will($this->throwException($exception))
        ;

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($message)
        ;

        // Run command
        $result = $this->command->run(
            $this->input,
            $this->output
        );

        // Assertions
        $this->assertEquals(
            $result,
            AbstractAlgoliaCommand::STATUS_CODE_ERROR
        );
    }
}

class ListIndexesCommandStub extends ListIndexesCommand
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
