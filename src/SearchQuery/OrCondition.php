<?php declare(strict_types=1);
namespace JBuncle\BitbucketClient\SearchQuery;

class OrCondition extends AbstractConditionAggregator {

    protected function getCondition(): string {
        return 'OR';
    }

}
