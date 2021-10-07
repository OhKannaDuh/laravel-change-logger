<?php

namespace Tests\Behaviours;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait TracksQueries
{
    protected $queries;

    public function bootTracksQueries(): void
    {
        $this->queries = new Collection();
        DB::listen(fn (QueryExecuted $query) => $this->queries->add([
            'query' => $query->sql,
            'bindings' => $query->bindings,
        ]));
    }

    /**
     * @param int $count
     *
     * @return void
     */
    public function assertQueryCount(int $count): void
    {
        $this->assertCount($count, $this->queries);
    }

    /**
     * @param int $count
     * @param string $field
     * @param string $value
     *
     * @return void
     */
    public function assertQueryCountWhere(int $count, string $field, string $value): void
    {
        $this->assertCount($count, $this->queries->where($field, $value));
    }

    /**
     * @param string $query
     *
     * @return void
     */
    public function assertQueryExists(string $query, array $bindings = []): void
    {
        $exists = $this->queries->contains('query', $query);
        $this->assertTrue($exists);
        if (!$bindings || !$exists) {
            return;
        }

        $log = $this->queries->where('query', $query)->first();
        $this->assertEquals($log['bindings'], $bindings);
    }
}
