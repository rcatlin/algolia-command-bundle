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
    private $apiKey;
    private $applicationId;

    private $input;
    private $output;
    private $client;
    private $command;

    protected function setUp()
    {
        $this->apiKey = 'api-key';
        $this->applicationId = 'application-id';

        $this->client = $this->buildMock('AlgoliaSearch\Client');
        $this->input = $this->buildMock('Symfony\Component\Console\Input\InputInterface');
        $this->output = $this->buildMock('Symfony\Component\Console\Output\OutputInterface');

        $this->command = new ListIndexesCommandStub(
            $this->apiKey,
            $this->applicationId
        );

        $this->command->setClient($this->client);
    }

    public function testExecute()
    {
        $answer = 'list-indexes-answer';

        $this->client->expects($this->once())
            ->method('listIndexes')
            ->will($this->returnValue($answer))
        ;

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($answer)
        ;

        $result = $this->command->callProtectedExecute(
            $this->input,
            $this->output
        );

        $this->assertSame(
            $result,
            null
        );
    }

    public function testExecuteThrowsException()
    {
        $message ='algolia-exception-message';
        $exception = new AlgoliaException($message);

        $this->client->expects($this->once())
            ->method('listIndexes')
            ->will($this->throwException($exception))
        ;

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($message)
        ;

        $result = $this->command->callProtectedExecute(
            $this->input,
            $this->output
        );

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

    public function callProtectedExecute(InputInterface $input, OutputInterface $output)
    {
        return $this->execute($input, $output);
    }
}