<?php

namespace AlgoliaCommandBundle\Tests;

class AlgoliaCommandBundleTestCase extends \PHPUnit_Framework_TestCase
{
    protected function buildMock($class)
    {
        return $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock();
    }   
}