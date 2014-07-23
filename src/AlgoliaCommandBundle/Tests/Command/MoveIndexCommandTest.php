<?php

namespace AlgoliaCommandBundle\Tests\Command;

use AlgoliaCommandBundle\Command\AbstractAlgoliaCommand;
use AlgoliaCommandBundle\Command\MoveIndexCommand;
use AlgoliaCommandBundle\Tests\AlgoliaCommandBundleTestCase;
use AlgoliaSearch\Client;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;

class MoveIndexCommandTest extends AlgoliaCommandBundleTestCase
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

        $this->command = new MoveIndexCommandStub();
        $this->command->setClient($this->client);
        $this->command->setContainer($this->container);
    }

    public function testExecute()
    {
        $answer = 'move-index-answer';
        $srcName = 'source-index-name';
        $destName = 'destination-index-name';

        $arguments = array(
            MoveIndexCommand::NAME,
            $srcName,
            $destName
        );
        $input = new ArgvInput($arguments);

        // Expectations
        $this->client->expects($this->once())
            ->method('moveIndex')
            ->with($srcName, $destName)
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
        $input = new ArgvInput(
            array(
                MoveIndexCommand::NAME
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
            AbstractAlgoliaCommand::STATUS_CODE_ERROR
        );
    }

    /**
     * @expectedException RuntimeException
     * @expectedException Not enough arguments.
     */
    public function testExecuteWithoutDestinationIndexNameThrowsException()
    {
        $srcName = 'source-index-name';

        $input = new ArgvInput(
            array(
                MoveIndexCommand::NAME,
                $srcName
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
            AbstractAlgoliaCommand::STATUS_CODE_ERROR
        );
    }
}

class MoveIndexCommandStub extends MoveIndexCommand
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
