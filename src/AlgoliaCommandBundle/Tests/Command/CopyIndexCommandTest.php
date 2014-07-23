<?php

namespace AlgoliaCommandBundle\Tests\Command;

use AlgoliaCommandBundle\Command\AbstractAlgoliaCommand;
use AlgoliaCommandBundle\Command\CopyIndexCommand;
use AlgoliaCommandBundle\Tests\AlgoliaCommandBundleTestCase;
use AlgoliaSearch\Client;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;

class CopyIndexCommandTest extends AlgoliaCommandBundleTestCase
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

        $this->command = new CopyIndexCommandStub();
        $this->command->setClient($this->client);
        $this->command->setContainer($this->container);
    }

    public function testExecute()
    {
        $answer = 'copy-index-answer';
        $srcName = 'source-index-name';
        $destName = 'destination-index-name';

        $arguments = array(
            CopyIndexCommand::NAME,
            $srcName,
            $destName
        );
        $input = new ArgvInput($arguments);

        // Expectations
        $this->client->expects($this->once())
            ->method('copyIndex')
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
                CopyIndexCommand::NAME
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
                CopyIndexCommand::NAME,
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

class CopyIndexCommandStub extends CopyIndexCommand
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
