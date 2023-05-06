<?php

namespace MortenScheel\QueryStats\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \MortenScheel\QueryStats\QueryStats enable()
 * @method static \MortenScheel\QueryStats\QueryStats disable()
 * @method static \MortenScheel\QueryStats\QueryStats reset()
 * @method static \MortenScheel\QueryStats\QueryStats logChannel(string $channel = null)
 * @method static \MortenScheel\QueryStats\QueryStats showAllQueries(bool $show = true)
 * @method static array getStats(bool $reset = false)
 */
class QueryStats extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'query-stats';
    }
}
