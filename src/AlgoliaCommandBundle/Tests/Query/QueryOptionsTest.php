<?php

namespace AlgoliaCommandBundle\Tests\Index;

use AlgoliaCommandBundle\Query\QueryOptions;
use AlgoliaCommandBundle\Tests\AlgoliaCommandBundleTestCase;

class QueryOptionsTest extends AlgoliaCommandBundleTestCase
{
    /**
     * @dataProvider evaluateProvider
     */
    public function testEvaluate($key, $value, $expected)
    {
        $result = QueryOptions::evaluate($key, $value);

        $this->assertEquals(
            $expected,
            $result
        );
    }

    public function evaluateProvider()
    {
        return array(
            array(
                QueryOptions::QUERY_TYPE, // TYPE_STRING
                5055,
                '5055'
            ),
            array(
                QueryOptions::ANALYTICS, // TYPE_BOOLEAN
                'true',
                true
            ),
            array(
                QueryOptions::SYNONYMS, // TYPE_BOOLEAN
                '0',
                false
            ),
            array(
                QueryOptions::GET_RANKING_INFO, // TYPE_INTEGER
                '33',
                33
            )
        );
    }
}
