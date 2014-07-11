<?php

namespace AlgoliaCommandBundle\Tests\Command;

use AlgoliaCommandBundle\Command\AbstractAlgoliaCommand;
use AlgoliaCommandBundle\Command\AddObjectCommand;
use AlgoliaCommandBundle\Tests\AlgoliaCommandBundleTestCase;
use AlgoliaSearch\AlgoliaException;
use AlgoliaSearch\Client;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;

class AddObjectCommandTest extends AlgoliaCommandBundleTestCase
{
    protected function setUp()
    {
        $this->output = $this->buildMock('Symfony\Component\Console\Output\OutputInterface');
        $this->client = $this->buildMock('AlgoliaSearch\Client');

        $this->container = $this->getDefaultContainer($this->apiKey, $this->applicationId);

        $this->command = new AddObjectCommandStub();
        $this->command->setClient($this->client);
        $this->command->setContainer($this->container);
    }

    public function testExecute()
    {
        $answer = 'add-object-answer';
        $indexName = 'add-object-index-name';
        $id = 'object-id';
        $content = array(
            'name' => 'Bill Waterson',
            'interests' => array(
                'drawing',
                'tigers',
                'the universe',
                'sledding'
            )
        );
        $jsonContent = json_encode($content);
        $input = new ArgvInput(
            array(
                AddObjectCommand::NAME,
                $indexName,
                $jsonContent,
                '--' . AddObjectCommand::OPTION_ID . '=' . $id,
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
            ->method('addObject')
            ->with($content, $id)
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
                AddObjectCommand::NAME
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
    public function testExcecuteWithoutContentThrowsException()
    {
        $input = new ArgvInput(
            array(
                AddObjectCommand::NAME,
                'index-name'
            )
        );
        // Run command
        $result = $this->command->run(
            $input,
            $this->output
        );

        // Assertions
        $this->assertEQuals(
            $result,
            null
        );
    }

    public function testExcecuteWithBadJson()
    {
        $input = new ArgvInput(
            array(
                AddObjectCommand::NAME,
                'index-name',
                '{"bad":"json"'

            )
        );

        // Expectations
        $this->output->expects($this->once())
            ->method('writeln')
            ->with(AddObjectCommand::ERROR_BAD_JSON_MESSAGE)
        ;

        $this->client->expects($this->never())
            ->method('initIndex')
        ;

        // Run command
        $result = $this->command->run(
            $input,
            $this->output
        );

        // Assertions
        $this->assertEQuals(
            $result,
            AbstractAlgoliaCommand::STATUS_CODE_ERROR
        );
    }

    public function testExecuteIndexThrowsException()
    {
        $indexName = 'index-name';
        $content = array(
            'awesome' => 'content'
        );
        $input = new ArgvInput(
            array(
                AddObjectCommand::NAME,
                $indexName,
                json_encode($content)
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
            ->method('addObject')
            ->with($content, null)
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
            AddObjectCommand::STATUS_CODE_ERROR
        );
    }
}

class AddObjectCommandStub extends AddObjectCommand
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
