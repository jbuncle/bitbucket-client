<?php declare(strict_types=1);
namespace JBuncle\BitbucketClient\SearchQuery;

abstract class AbstractBinaryCondition implements ConditionI {

    private string $field;
    private string $value;

    public function __construct(string $field, string $value) {
        $this->field = $field;
        $this->value = $value;
    }

    public function __toString(): string {
        return $this->field . ' ' . $this->getOperator() . ' "' . str_replace('"', '\"', $this->value) . '"';
    }

    protected abstract function getOperator(): string;

}
