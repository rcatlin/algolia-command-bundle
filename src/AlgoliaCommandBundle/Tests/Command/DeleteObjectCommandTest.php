<?php

namespace AlgoliaCommandBundle\Tests\Command;

use AlgoliaCommandBundle\Command\DeleteObjectCommand;
use AlgoliaCommandBundle\Tests\AlgoliaCommandBundleTestCase;
use AlgoliaSearch\AlgoliaException;
use AlgoliaSearch\Client;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteObjectCommandTest extends AlgoliaCommandBundleTestCase
{
    protected function setUp()
    {
        $this->output = $this->buildMock('Symfony\Component\Console\Output\OutputInterface');
        $this->client = $this->buildMock('AlgoliaSearch\Client');

        $this->container = $this->getDefaultContainer($this->apiKey, $this->applicationId);

        $this->command = new DeleteObjectCommandStub();
        $this->command->setClient($this->client);
        $this->command->setContainer($this->container);
    }

    public function testExecute()
    {
        $answer = 'delete-object-answer';
        $indexName = 'delete-object-index-name';
        $id = 'object-id';
        $input = new ArgvInput(
            array(
                DeleteObjectCommand::NAME,
                $indexName,
                $id,
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
            ->method('deleteObject')
            ->with($id)
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
                DeleteObjectCommand::NAME
            )
        );
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
    public function testExecuteWithoutIdThrowsException()
    {
        $indexName = 'index-name';
        $input = new ArgvInput(
            array(
                DeleteObjectCommand::NAME,
                $indexName
            )
        );

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

    public function testExecuteIndexThrowsException()
    {
        $indexName = 'index-name';
        $id = 'object-id';
        $input = new ArgvInput(
            array(
                DeleteObjectCommand::NAME,
                $indexName,
                $id
            )
        );
        $index = $this->buildMock('AlgoliaSearch\Index');
        $exceptionMessage = 'exception-message';

        // Expectations
        $this->client->expects($this->once())
            ->method('initIndex')
            ->with($indexName)
            ->will($this->returnValue($index))
        ;

        $index->expects($this->once())
            ->method('deleteObject')
            ->with($id)
            ->will(
                $this->throwException(
                    new AlgoliaException($exceptionMessage)
                )
            )
        ;

        $this->output->expects($this->once())
            ->method('writeln')
            ->with($exceptionMessage)
        ;

        // Run Command
        $result = $this->command->run($input, $this->output);

        // Assertions
        $this->assertEquals(
            $result,
            DeleteObjectCommand::STATUS_CODE_ERROR
        );
    }
}

class DeleteObjectCommandStub extends DeleteObjectCommand
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
