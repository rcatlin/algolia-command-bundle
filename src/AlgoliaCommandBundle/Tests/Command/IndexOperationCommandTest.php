<?php

namespace AlgoliaCommandBundle\Tests\Command;

use AlgoliaSearch\Client;
use AlgoliaCommandBundle\Command\AbstractAlgoliaCommand;
use AlgoliaCommandBundle\Command\IndexOperationCommand;
use AlgoliaCommandBundle\Tests\AlgoliaCommandBundleTestCase;
use Symfony\Component\Console\Input\ArgvInput;

class IndexOperationCommandTest extends AlgoliaCommandBundleTestCase
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

        $this->command = new IndexOperationCommandStub();
        $this->command->setClient($this->client);
        $this->command->setContainer($this->container);
    }

    public function testExecuteMove()
    {
        $srcIndexName = 'src-index-name';
        $destIndexName = 'dest-index-name';
        $answer = 'move-index-result';

        // Setup Input
        $input = new ArgvInput(
            array(
                IndexOperationCommand::NAME,
                $srcIndexName,
                $destIndexName,
                '--' . IndexOperationCommand::OPTION_MOVE,
            )
        );

        // Expectations
        $this->client->expects($this->once())
            ->method(IndexOperationCommand::CLIENT_METHOD_MOVE)
            ->with($srcIndexName, $destIndexName)
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

    public function testExecuteCopy()
    {
        $srcIndexName = 'src-index-name';
        $destIndexName = 'dest-index-name';
        $answer = 'copy-index-result';

        // Setup Input
        $input = new ArgvInput(
            array(
                IndexOperationCommand::NAME,
                $srcIndexName,
                $destIndexName,
                '--' . IndexOperationCommand::OPTION_COPY,
            )
        );

        // Expectations
        $this->client->expects($this->once())
            ->method(IndexOperationCommand::CLIENT_METHOD_COPY)
            ->with($srcIndexName, $destIndexName)
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

    public function testExecuteWithBothOptions()
    {
        $srcIndexName = 'src-index-name';
        $destIndexName = 'dest-index-name';
        $answer = 'copy-index-result';

        // Setup Input
        $input = new ArgvInput(
            array(
                IndexOperationCommand::NAME,
                $srcIndexName,
                $destIndexName,
                '--' . IndexOperationCommand::OPTION_COPY,
                '--' . IndexOperationCommand::OPTION_MOVE,
            )
        );

        // Expectations
        $this->client->expects($this->never())
            ->method(IndexOperationCommand::CLIENT_METHOD_COPY)
        ;

        $this->client->expects($this->never())
            ->method(IndexOperationCommand::CLIENT_METHOD_MOVE)
        ;

        $this->output->expects($this->once())
            ->method('writeln')
            ->with(IndexOperationCommand::ERROR_MESSAGE_BOTH_OPTIONS)
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

    public function testExecuteWithNoOptions()
    {
        $srcIndexName = 'src-index-name';
        $destIndexName = 'dest-index-name';
        $answer = 'copy-index-result';

        // Setup Input
        $input = new ArgvInput(
            array(
                IndexOperationCommand::NAME,
                $srcIndexName,
                $destIndexName,
            )
        );

        // Expectations
        $this->client->expects($this->never())
            ->method(IndexOperationCommand::CLIENT_METHOD_COPY)
        ;

        $this->client->expects($this->never())
            ->method(IndexOperationCommand::CLIENT_METHOD_MOVE)
        ;

        $this->output->expects($this->once())
            ->method('writeln')
            ->with(IndexOperationCommand::ERROR_MESSAGE_NO_OPTIONS)
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

    /**
     * @expectedException RuntimeException
     * @expectedException Not enough arguments.
     */
    public function testExecuteMissingIndexNamesThrowsException()
    {
        // Run command
        $result = $this->command->run(
            new ArgvInput(
                array(
                    IndexOperationCommand::NAME,
                )
            ),
            $this->output
        );
    }

    /**
     * @expectedException RuntimeException
     * @expectedException Not enough arguments.
     */
    public function testExecuteMissingDestinationIndexNameThrowsException()
    {
        // Run command
        $result = $this->command->run(
            new ArgvInput(
                array(
                    IndexOperationCommand::NAME,
                    'src-index-name',
                )
            ),
            $this->output
        );
    }
}

class IndexOperationCommandStub extends IndexOperationCommand
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
