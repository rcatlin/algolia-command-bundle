<?php

namespace AlgoliaCommandBundle\Tests\Command;

use AlgoliaCommandBundle\Command\AbstractAlgoliaCommand;
use AlgoliaCommandBundle\Command\BatchWriteCommand;
use AlgoliaCommandBundle\Tests\AlgoliaCommandBundleTestCase;
use AlgoliaSearch\AlgoliaException;
use AlgoliaSearch\Client;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;

class BatchWriteCommandTest extends AlgoliaCommandBundleTestCase
{
    protected function setUp()
    {
        $this->output = $this->buildMock('Symfony\Component\Console\Output\OutputInterface');
        $this->client = $this->buildMock('AlgoliaSearch\Client');

        $this->container = $this->getDefaultContainer($this->apiKey, $this->applicationId);

        $this->command = new BatchWriteCommandStub();
        $this->command->setClient($this->client);
        $this->command->setContainer($this->container);
    }

    public function testExecute()
    {
        $answer = 'answer';
        $indexName = 'index-name';
        $objectIDKey = 'object-id-key';
        $objectActionKey = 'object-action-key';
        $requests = array(
            array(
                $objectIDKey => 'id0',
                $objectActionKey => 'action0',
                'vegetable' => 'tomato'
            ),
            array(
                $objectIDKey => 'id1',
                $objectActionKey => 'action1',
                'vegetable' => 'celery'
            )
        );

        $input = new ArgvInput(
            array(
                BatchWriteCommand::NAME,
                $indexName,
                json_encode($requests),
                '--' . BatchWriteCommand::ARGUMENT_OBJECT_ID_KEY . '=' . $objectIDKey,
                '--' . BatchWriteCommand::ARGUMENT_OBJECT_ACTION_KEY . '=' . $objectActionKey
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
            ->method('batchObjects')
            ->with($requests, $objectIDKey, $objectActionKey)
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

    public function testExecuteWithInvalidJson()
    {
        $badRequestJson = '{"bad":"json"';
        $input = new ArgvInput(
            array(
                BatchWriteCommand::NAME,
                'index-name',
                $badRequestJson
            )
        );

        // Expectations
        $this->output->expects($this->once())
            ->method('writeln')
            ->with(AbstractAlgoliaCommand::ERROR_BAD_JSON_MESSAGE)
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

    public function testExecuteIndexThrowsException()
    {
        $indexName = 'index-name';
        $objectIDKey = 'object-id-key';
        $objectActionKey = 'object-action-key';
        $requests = array(
            array(
                $objectIDKey => 'id0',
                $objectActionKey => 'action0',
                'vegetable' => 'tomato'
            ),
            array(
                $objectIDKey => 'id1',
                $objectActionKey => 'action1',
                'vegetable' => 'celery'
            )
        );
        $exceptionMessage = 'exception-message';

        $input = new ArgvInput(
            array(
                BatchWriteCommand::NAME,
                $indexName,
                json_encode($requests),
                '--' . BatchWriteCommand::ARGUMENT_OBJECT_ID_KEY . '=' . $objectIDKey,
                '--' . BatchWriteCommand::ARGUMENT_OBJECT_ACTION_KEY . '=' . $objectActionKey
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
            ->method('batchObjects')
            ->with($requests, $objectIDKey, $objectActionKey)
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

class BatchWriteCommandStub extends BatchWriteCommand
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
