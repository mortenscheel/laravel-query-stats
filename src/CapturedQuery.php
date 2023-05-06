<?php

namespace MortenScheel\QueryStats;

class CapturedQuery
{
    /** @var array<int, float> */
    public array $times = [];

    public function __construct(
        public string $sql,
        public int $count = 0
    ) {
    }

    public function totalTime(): float
    {
        return round(array_sum($this->times), 4);
    }
}
