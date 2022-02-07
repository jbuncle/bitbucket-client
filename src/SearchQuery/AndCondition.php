<?php declare(strict_types=1);
namespace JBuncle\BitbucketClient\SearchQuery;

class AndCondition extends AbstractConditionAggregator {

    protected function getCondition(): string {
        return 'AND';
    }

}
