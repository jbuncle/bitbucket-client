<?php declare(strict_types=1);
namespace JBuncle\BitbucketClient\SearchQuery;

abstract class AbstractConditionAggregator implements ConditionI {

    /**
     *
     * @var array<ConditionI>
     */
    private array $conditions;

    public function __construct(ConditionI ...$conditions) {
        $this->conditions = [];
        $this->addConditions(...$conditions);
    }

    public function addConditions(ConditionI ...$conditions): void {
        foreach ($conditions as $condition) {
            $this->addCondition($condition);
        }
    }

    public function addCondition(ConditionI $condition): void {
        $this->conditions[] = $condition;
    }

    public function __toString(): string {

        return '(' . implode(' ' . $this->getCondition() . ' ', $this->conditions) . ')';
    }

    protected abstract function getCondition(): string;

}
