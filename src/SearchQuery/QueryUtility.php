<?php declare(strict_types=1);
namespace JBuncle\BitbucketClient\SearchQuery;

class QueryUtility {

    public function __construct() {
    }

    public static function toInQuery(string $field, array $values): ConditionI {
        $conditions = array_map(function (string $value) use ($field): ConditionI {
            return new EqualsCondition($field, $value);
        }, $values);

        return new OrCondition(...$conditions);
    }

    public static function toNotInQuery(string $field, array $values): ConditionI {
        $conditions = array_map(function (string $value) use ($field): ConditionI {
            return new NotEqualsCondition($field, $value);
        }, $values);

        return new AndCondition(...$conditions);
    }

}
