<?php

namespace AlgoliaCommandBundle\Query;

class QueryOptions
{
    // full text search parameters
    CONST QUERY_TYPE                    = 'queryType';
    CONST TYPO_TOLERANCE                = 'typoTolerance';
    CONST MIN_WORD_SIZE_FOR_1_TYPO      = 'minWordSizefor1Typo';
    CONST MIN_WORD_SIZE_FOR_2_TYPOS     = 'minWordSizefor2Typos';
    CONST ALLOW_TYPOS_ON_NUMERIC_TOKENS = 'allowTyposOnNumericTokens';
    CONST ADVANCED_SYNTAX               = 'advancedSyntax';
    CONST ANALYTICS                     = 'analytics';
    CONST SYNONYMS                      = 'synonyms';
    CONST REPLACE_SYNONYMS_IN_HIGHLIGHT = 'replaceSynonymsInHighlight';
    CONST OPTIONAL_WORDS                = 'optionalWords';

    // pagination parameters
    CONST PAGE          = 'page';
    CONST HITS_PER_PAGE = 'hitsPerPage';

    // parameters to control results content
    CONST ATTRIBUTES_TO_RETRIEVE  = 'attributesToRetrieve';
    CONST ATTRIBUTES_TO_HIGHLIGHT = 'attributesToHighlight';
    CONST ATTRIBUTES_TO_SNIPPET   = 'attributesToSnippet';
    CONST GET_RANKING_INFO        = 'getRankingInfo';

    // numeric search parameters
    CONST NUMERIC_FILTERS = 'numericFilters';

    // category search parameter
    CONST TAG_FILTERS = 'tagFilters';

    // distinct paramter
    CONST DISTINCT = 'distinct';

    // faceting parameters
    CONST FACETS               = 'facets';
    CONST FACET_FILTERS        = 'facetFilters';
    CONST MAX_VALUES_PER_FACET = 'maxValuesPerFacet';

    // geo-search parameters
    CONST AROUND_LAT_LNG      = 'aroundLatLng';
    CONST AROUND_RADIUS       = 'aroundRadius';
    CONST AROUND_PRECISION    = 'aroundPrecision';
    CONST INSIDE_BOUNDING_BOX = 'insideBoundingBox';

    // option casting types
    CONST TYPE_STRING  = 'string';
    CONST TYPE_INTEGER = 'integer';
    CONST TYPE_BOOLEAN = 'boolean';

    public static $all = array(
        self::QUERY_TYPE,
        self::TYPO_TOLERANCE,
        self::MIN_WORD_SIZE_FOR_1_TYPO,
        self::MIN_WORD_SIZE_FOR_2_TYPOS,
        self::ALLOW_TYPOS_ON_NUMERIC_TOKENS,
        self::ADVANCED_SYNTAX,
        self::ANALYTICS,
        self::SYNONYMS,
        self::REPLACE_SYNONYMS_IN_HIGHLIGHT,
        self::OPTIONAL_WORDS,
        self::PAGE,
        self::HITS_PER_PAGE,
        self::ATTRIBUTES_TO_RETRIEVE,
        self::ATTRIBUTES_TO_HIGHLIGHT,
        self::ATTRIBUTES_TO_SNIPPET,
        self::GET_RANKING_INFO,
        self::NUMERIC_FILTERS,
        self::TAG_FILTERS,
        self::DISTINCT,
        self::FACETS,
        self::FACET_FILTERS,
        self::MAX_VALUES_PER_FACET,
        self::AROUND_LAT_LNG,
        self::AROUND_RADIUS,
        self::AROUND_PRECISION,
        self::INSIDE_BOUNDING_BOX,
    );

    public static $types = array(
        self::QUERY_TYPE =>                    self::TYPE_STRING,
        self::TYPO_TOLERANCE =>                self::TYPE_BOOLEAN,
        self::MIN_WORD_SIZE_FOR_1_TYPO =>      self::TYPE_INTEGER,
        self::MIN_WORD_SIZE_FOR_2_TYPOS =>     self::TYPE_INTEGER,
        self::ALLOW_TYPOS_ON_NUMERIC_TOKENS => self::TYPE_BOOLEAN,
        self::ADVANCED_SYNTAX =>               self::TYPE_BOOLEAN,
        self::ANALYTICS =>                     self::TYPE_BOOLEAN,
        self::SYNONYMS =>                      self::TYPE_BOOLEAN,
        self::REPLACE_SYNONYMS_IN_HIGHLIGHT => self::TYPE_BOOLEAN,
        self::OPTIONAL_WORDS =>                self::TYPE_STRING,
        self::PAGE =>                          self::TYPE_INTEGER,
        self::HITS_PER_PAGE =>                 self::TYPE_INTEGER,
        self::ATTRIBUTES_TO_RETRIEVE =>        self::TYPE_STRING,
        self::ATTRIBUTES_TO_HIGHLIGHT =>       self::TYPE_STRING,
        self::ATTRIBUTES_TO_SNIPPET =>         self::TYPE_STRING,
        self::GET_RANKING_INFO =>              self::TYPE_INTEGER,
        self::NUMERIC_FILTERS =>               self::TYPE_STRING,
        self::TAG_FILTERS =>                   self::TYPE_STRING,
        self::DISTINCT =>                      self::TYPE_BOOLEAN,
        self::FACETS =>                        self::TYPE_STRING,
        self::FACET_FILTERS =>                 self::TYPE_STRING,
        self::MAX_VALUES_PER_FACET =>          self::TYPE_INTEGER,
        self::AROUND_LAT_LNG =>                self::TYPE_STRING,
        self::AROUND_RADIUS =>                 self::TYPE_INTEGER,
        self::AROUND_PRECISION =>              self::TYPE_INTEGER,
        self::INSIDE_BOUNDING_BOX =>           self::TYPE_STRING,
    );

    public static function evaluate($key, $value)
    {
        if (!isset(self::$types[$key])) {
            return $value;
        }

        if (isset(self::$types[$key])) {
            return self::cast(
                self::$types[$key],
                $value
            );
        }

        return $value;
    }

    private static function cast($type, $value)
    {
        switch ($type) {
            case self::TYPE_BOOLEAN:
                return (boolean) $value;
            case self::TYPE_INTEGER:
                return intval($value);
            case self::TYPE_STRING:
                return strval($value);
        }

        return $value;
    }
}
