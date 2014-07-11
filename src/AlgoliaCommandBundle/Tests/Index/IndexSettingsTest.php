<?php

namespace AlgoliaCommandBundle\Tests\Index;

use AlgoliaCommandBundle\Index\IndexSettings;
use AlgoliaCommandBundle\Tests\AlgoliaCommandBundleTestCase;

class IndexSettingsTest extends AlgoliaCommandBundleTestCase
{
    /**
     * @dataProvider evaluateProvider
     */
    public function testEvaluate($key, $value, $expected)
    {
        $result = IndexSettings::evaluate($key, $value);

        $this->assertEquals(
            $expected,
            $result
        );
    }

    public function evaluateProvider()
    {
        return array(
            array(
                IndexSettings::ATTRIBUTES_TO_INDEX, // TYPE_STRING_ARRAY
                array('name', 'age', 3, true, false),
                array('name', 'age', '3', '1', '0')
            ),
            array(
                IndexSettings::ATTRIBUTE_FOR_DISTINCT, // TYPE_STRING
                'title',
                'title'
            ),
            array(
                IndexSettings::SYNONYMS, // TYPE_ARRAY_OF_STRINGS_ARRAY
                array(
                    array('string0', 'string1', true, 33),
                    array('mercury', 'venus', 'mars', 'earth')
                ),
                array(
                    array('string0', 'string1', '1', '33'),
                    array('mercury', 'venus', 'mars', 'earth')
                )
            ),
            array(
                IndexSettings::PLACEHOLDERS, // TYPE_HASH_STRING_TO_ARRAY_OF_STRINGS
                array(
                    0 => array(1, 2, 3),
                    '1' => array(true, false),
                    'sharks' => array('great white', 'mako')
                ),
                array(
                    '0' => array('1', '2', '3'),
                    '1' => array('1', '0'),
                    'sharks' => array('great white', 'mako')
                )
            ),
            array(
                IndexSettings::HITS_PER_PAGE, // TYPE_INTEGER
                '2048',
                2048
            )
        );
    }
}
