<?php

declare(strict_types=1);

namespace MortenScheel\QueryStats;

use DateTimeInterface;
use DB;
use Illuminate\Database\Events\QueryExecuted;
use Log;
use Str;

final class QueryStats
{
    private bool $showAllQueries = false;

    private ?string $logChannel = null;

    /** @var array<string, \MortenScheel\QueryStats\CapturedQuery> */
    private array $capturedQueries = [];

    private bool $enabled = true;

    public function __construct()
    {
        DB::listen(fn (QueryExecuted $event) => $this->captureQuery($event));
    }

    public function __destruct()
    {
        if (! empty($this->capturedQueries)) {
            foreach ($this->capturedQueries as $query) {
                if ($query->count > 1) {
                    Log::channel($this->logChannel)->info("Duplicate query: $query->sql", [
                        'count' => $query->count,
                        'time' => $query->totalTime(),
                    ]);
                }
            }
            Log::channel($this->logChannel)->info('Query stats', $this->getStats());
        }
    }

    /**
     * Reset the QueryStats instance
     */
    public function reset(): self
    {
        $this->capturedQueries = [];
        $this->enabled = true;

        return $this;
    }

    /**
     * @param  bool  $reset Reset captured queries
     * @return array{count: int, time: float, duplicates: int, unique: int}
     */
    public function getStats(bool $reset = false): array
    {
        /** @var array{count: int, time: float, duplicates: int, unique: int} $stats */
        $stats = collect($this->capturedQueries)->reduce(function (array $carry, CapturedQuery $query) {
            $carry['count'] += $query->count;
            $carry['time'] += $query->totalTime();
            if ($query->count > 1) {
                $carry['duplicates'] += $query->count - 1;
            }
            $carry['unique']++;

            return $carry;
        }, ['count' => 0, 'time' => 0.0, 'duplicates' => 0, 'unique' => 0]);
        $stats['time'] = round($stats['time'], 4);

        if ($reset) {
            $this->reset();
        }

        return $stats;
    }

    /**
     * Enable query capturing
     */
    public function enable(): self
    {
        $this->enabled = true;

        return $this;
    }

    /**
     * Suspend query capturing
     */
    public function disable(): self
    {
        $this->enabled = false;

        return $this;
    }

    private function captureQuery(QueryExecuted $event): void
    {
        if (! $this->enabled) {
            return;
        }
        $inlined = $this->inlineBindings($event->sql, $event->bindings);
        $captured = $this->capturedQueries[$inlined] ??= new CapturedQuery($inlined);
        $captured->count++;
        $captured->times[] = $event->time;
        if ($this->showAllQueries) {
            Log::channel($this->logChannel)->info($inlined, ['time' => $event->time]);
        }
    }

    /**
     * @param  array<int, string|int|bool|double|DateTimeInterface>  $bindings
     */
    private function inlineBindings(string $sql, array $bindings): string
    {
        $formattedBindings = array_map(function (mixed $binding) {
            if ($binding instanceof DateTimeInterface) {
                $binding = $binding->format('Y-m-d H:i:s');
            }
            if (is_bool($binding)) {
                $binding = (int) $binding;
            }
            if (is_string($binding)) {
                $binding = DB::getPdo()->quote($binding);
            }

            return (string) $binding;
        }, $bindings);

        return Str::replaceArray('?', $formattedBindings, $sql);
    }

    /**
     * @noinspection PhpUnused
     */
    public function showAllQueries(bool $show = true): self
    {
        $this->showAllQueries = $show;

        return $this;
    }

    /**
     * @noinspection PhpUnused
     */
    public function logChannel(string $channel = null): self
    {
        $this->logChannel = $channel;

        return $this;
    }
}
