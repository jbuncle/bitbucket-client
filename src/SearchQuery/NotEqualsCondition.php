<?php declare(strict_types=1);
namespace JBuncle\BitbucketClient\SearchQuery;

class NotEqualsCondition extends AbstractBinaryCondition {

    protected function getOperator(): string {
        return '!=';
    }

}
