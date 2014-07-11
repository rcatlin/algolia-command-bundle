<?php

namespace AlgoliaCommandBundle\Index;

class IndexSettings
{
    // Indexing paramters
    CONST ATTRIBUTES_TO_INDEX = 'attributesToIndex';
    CONST ATTRIBUTES_FOR_FACETING = 'attributesForFaceting';
    CONST ATTRIBUTE_FOR_DISTINCT = 'attributeForDistinct';
    CONST RANKING = 'ranking';
    CONST CUSTOM_RANKING = 'customRanking';
    CONST SEPARATORS_TO_INDEX = 'separatorsToIndex';
    CONST SLAVES = 'slaves';

    // Query expansion
    CONST SYNONYMS = 'synonyms';
    CONST PLACEHOLDERS = 'placeholders';
    CONST DISABLE_TYPO_TOLERANCE_ON = 'disableTypoToleranceOn';
    CONST ALT_CORRECTIONS = 'altCorrections';

    // Default query parmaters (can be override at query-time)
    CONST MIN_WORD_SIZE_FOR_1_TYPO = 'minWordSizefor1Typo';
    CONST MIN_WORD_SIZE_FOR_2_TYPOS = 'minWordSizefor2Typos';
    CONST HITS_PER_PAGE = 'hitsPerPage';
    CONST ATTRIBUTES_TO_RETRIEVE = 'attributesToRetrieve';
    CONST ATTRIBUTES_TO_HIGHLIGHT = 'attributesToHighlight';
    CONST ATTRIBUTES_TO_SNIPPET = 'attributesToSnippet';
    CONST QUERY_TYPE = 'queryType';
    CONST HIGHLIGHT_PRE_TAG = 'highlightPreTag';
    CONST HIGHLIGHT_POST_TAG = 'highlightPostTag';
    CONST OPTIONAL_WORDS = 'optionalWords';

    // setting casting types
    // option casting types
    CONST TYPE_STRING  = 'string';
    CONST TYPE_INTEGER = 'integer';
    CONST TYPE_STRING_ARRAY = 'string array';
    CONST TYPE_HASH_STRING_TO_ARRAY_OF_STRINGS = 'hash(string, array of strings)';
    CONST TYPE_OBJECT_ARRAY = 'object array';
    CONST TYPE_ARRAY_OF_STRING = 'array of string';
    CONST TYPE_ARRAY_OF_STRINGS_ARRAY = 'array of strings array';

    public static $all = array(
        self::ATTRIBUTES_TO_INDEX,
        self::ATTRIBUTES_FOR_FACETING,
        self::ATTRIBUTE_FOR_DISTINCT,
        self::RANKING,
        self::CUSTOM_RANKING,
        self::SEPARATORS_TO_INDEX,
        self::SLAVES,
        self::SYNONYMS,
        self::PLACEHOLDERS,
        self::DISABLE_TYPO_TOLERANCE_ON,
        self::ALT_CORRECTIONS,
        self::MIN_WORD_SIZE_FOR_1_TYPO,
        self::MIN_WORD_SIZE_FOR_2_TYPOS,
        self::HITS_PER_PAGE,
        self::ATTRIBUTES_TO_RETRIEVE,
        self::ATTRIBUTES_TO_HIGHLIGHT,
        self::ATTRIBUTES_TO_SNIPPET,
        self::QUERY_TYPE,
        self::HIGHLIGHT_PRE_TAG,
        self::HIGHLIGHT_POST_TAG,
        self::OPTIONAL_WORDS
    );

    public static $types = array(
        self::ATTRIBUTES_TO_INDEX => self::TYPE_STRING_ARRAY,
        self::ATTRIBUTES_FOR_FACETING => self::TYPE_STRING_ARRAY,
        self::ATTRIBUTE_FOR_DISTINCT => self::TYPE_STRING,
        self::RANKING => self::TYPE_STRING_ARRAY,
        self::CUSTOM_RANKING => self::TYPE_STRING_ARRAY,
        self::SEPARATORS_TO_INDEX => self::TYPE_STRING,
        self::SLAVES => self::TYPE_STRING_ARRAY,
        self::SYNONYMS => self::TYPE_ARRAY_OF_STRINGS_ARRAY,
        self::PLACEHOLDERS => self::TYPE_HASH_STRING_TO_ARRAY_OF_STRINGS,
        self::DISABLE_TYPO_TOLERANCE_ON => self::TYPE_STRING_ARRAY,
        self::ALT_CORRECTIONS => self::TYPE_OBJECT_ARRAY,
        self::MIN_WORD_SIZE_FOR_1_TYPO => self::TYPE_INTEGER,
        self::MIN_WORD_SIZE_FOR_2_TYPOS => self::TYPE_INTEGER,
        self::HITS_PER_PAGE => self::TYPE_INTEGER,
        self::ATTRIBUTES_TO_RETRIEVE => self::TYPE_STRING_ARRAY,
        self::ATTRIBUTES_TO_HIGHLIGHT => self::TYPE_STRING_ARRAY,
        self::ATTRIBUTES_TO_SNIPPET => self::TYPE_STRING_ARRAY,
        self::QUERY_TYPE => self::TYPE_STRING,
        self::HIGHLIGHT_PRE_TAG => self::TYPE_STRING,
        self::HIGHLIGHT_POST_TAG => self::TYPE_STRING,
        self::OPTIONAL_WORDS => self::TYPE_ARRAY_OF_STRING
    );

    public static function evaluate($key, $value)
    {
        if (!isset(self::$types[$key])) {
            return $value;
        }

        if (isset(self::$types[$key])) {
            return self::cast(self::$types[$key], $value);
        }

        return $value;
    }

    private static function cast($type, $value)
    {
        switch ($type) {
            case self::TYPE_INTEGER:
                return intval($value);
            case self::TYPE_STRING:
                if ($value === false) {
                    return '0';
                }

                return strval($value);
            case self::TYPE_STRING_ARRAY:
            case self::TYPE_ARRAY_OF_STRING:
                if (!is_array($value)) {
                    return array();
                }

                foreach ($value as $i => $j) {
                    $value[$i] = self::cast(self::TYPE_STRING, $j);
                }

                return $value;
            case self::TYPE_ARRAY_OF_STRINGS_ARRAY:
                if (!is_array($value)) {
                    return array();
                }

                foreach ($value as $i => $j) {
                    $value[$i] = self::cast(self::TYPE_STRING_ARRAY, $j);
                }

                return $value;
            case self::TYPE_HASH_STRING_TO_ARRAY_OF_STRINGS:
                if (!is_array($value)) {
                    return array();
                }

                $hash = array();
                foreach ($value as $i => $j) {
                    $hash[strval($i)] = self::cast(self::TYPE_ARRAY_OF_STRING, $j);
                }

                return $hash;
        }

        return $value;
    }
}
