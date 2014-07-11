<?php

namespace AlgoliaCommandBundle\Tests;

use AlgoliaCommandBundle\Command\AbstractAlgoliaCommand;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class AlgoliaCommandBundleTestCase extends \PHPUnit_Framework_TestCase
{
    protected $apiKey = 'api-key';
    protected $applicationId = 'application-id';

    protected function buildMock($class)
    {
        return $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getDefaultContainer()
    {
        return new Container(
            new ParameterBag(
                array(
                    AbstractAlgoliaCommand::PARAMETER_API_KEY => $this->apiKey,
                    AbstractAlgoliaCommand::PARAMETER_APPLICATION_ID => $this->applicationId
                )
            )
        );
    }
}
