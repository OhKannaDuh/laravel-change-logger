<?php

namespace Tests\Unit\Models\Behaviours;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\Concerns\InteractsWithTime;
use Illuminate\Support\Facades\Log;
use OhKannaDuh\ChangeLogger\ChangeLoggerInterface;
use OhKannaDuh\ChangeLogger\DatabaseChangeLogger;
use Tests\Behaviours\TracksQueries;
use Tests\Models\Spy;
use Tests\Models\SpyThatOnlyObserversName;
use Tests\Models\SpyWithDatabaseObserver;
use Tests\Models\SpyWithoutAnythingToLog;
use Tests\Models\SpyWithoutNameLog;
use Tests\TestCase;

final class LogsChangesTest extends TestCase
{
    use InteractsWithTime;
    use TracksQueries;

    /**
     * @return iterable
     */
    public function changeProvider(): iterable
    {
        // Original, Changes
        return [
            [
                [
                    'name' => 'John Bishop',
                    'alias' => 'Bishop',
                    'missions_complete' => 999,
                    'active' => true,
                ],
                [
                    'name' => 'Bob the Builder',
                    'missions_complete' => 1000,
                ],

            ],
            [
                [
                    'name' => 'John Bishop',
                    'alias' => 'Bishop',
                    'missions_complete' => 999,
                    'active' => true,
                ],
                [
                    'name' => 'Bob the Builder',
                    'missions_complete' => 2500,
                    'active' => false,
                ],

            ],
            [
                [
                    'name' => 'John Bishop',
                    'alias' => 'Bishop',
                    'missions_complete' => 999,
                    'active' => true,
                ],
                [
                    'name' => 'Bob the Builder',
                    'alias' => 'King',
                ],

            ],
        ];
    }

    /**
     * Ensure our change logger correctly logs the event with the correct attributes.
     *
     * @dataProvider changeProvider
     *
     * @param array $original
     * @param array $modified
     */
    public function testLogsChanges(array $original, array $modified): void
    {
        $changedAttributes = [];
        foreach ($modified as $attribute => $change) {
            $changedAttributes[$attribute] = [
                'original' => $original[$attribute],
                'new' => $change,
            ];
        }

        Log::shouldReceive('info')->with([
            'event' => 'Updated: [' . Spy::class . ']',
            'attributes' => $changedAttributes,
        ])->once();

        /** @var Spy $model */
        $model = Spy::create($original);
        $model->update($modified);
    }

    /**
     * Ensure our models can define attributes to observe.
     *
     * @dataProvider changeProvider
     *
     * @param array $original
     * @param array $modified
     */
    public function testObservedAttributes(array $original, array $modified): void
    {
        Log::shouldReceive('info')->with([
            'event' => 'Updated: [' . SpyThatOnlyObserversName::class . ']',
            'attributes' => [
                'name' => [
                    'original' => $original['name'],
                    'new' => $modified['name'],
                ]
            ],
        ])->once();

        /** @var SpyThatOnlyObserversName $model */
        $model = SpyThatOnlyObserversName::create($original);
        $model->update($modified);
    }

    /**
     * Ensure our models can define attributes to ignore.
     *
     * @dataProvider changeProvider
     *
     * @param array $original
     * @param array $modified
     */
    public function testWithoutAttributes(array $original, array $modified): void
    {
        $changedAttributes = [];
        foreach ($modified as $attribute => $change) {
            $changedAttributes[$attribute] = [
                'original' => $original[$attribute],
                'new' => $change,
            ];
        }

        unset($changedAttributes['name']);

        Log::shouldReceive('info')->with([
            'event' => 'Updated: [' . SpyWithoutNameLog::class . ']',
            'attributes' => $changedAttributes,
        ])->once();

        /** @var SpyWithoutNameLog $model */
        $model = SpyWithoutNameLog::create($original);
        $model->update($modified);
    }

    /**
     * Ensure we don't log if nothing has changed.
     *
     * @dataProvider changeProvider
     *
     * @param array $original
     * @param array $modified
     */
    public function testNoLogWithoutAllAttributes(array $original, array $modified): void
    {
        Log::shouldReceive('info')->never();

        /** @var SpyWithoutAnythingToLog $model */
        $model = SpyWithoutAnythingToLog::create($original);
        $model->update($modified);
    }

    /**
     * Ensure we don't log if nothing has changed database.
     *
     * @dataProvider changeProvider
     *
     * @param array $original
     * @param array $modified
     */
    public function testNoLogWithoutAllAttributesDatabase(array $original, array $modified): void
    {
        $this->app->bind(ChangeLoggerInterface::class, DatabaseChangeLogger::class);
        /** @var SpyWithoutAnythingToLog $model */
        $model = SpyWithoutAnythingToLog::create($original);
        $model->update($modified);

        $query = 'insert into "model_change_log" ' .
            '("model", "original", "changes", "updated_at", "created_at") ' .
            'values (?, ?, ?, ?, ?)';
        $this->assertQueryCountWhere(0, 'query', $query);
    }

    /**
     * Ensure we do log with no attributes if the config is set to.
     *
     * @dataProvider changeProvider
     *
     * @param array $original
     * @param array $modified
     */
    public function testLogWithNoAttributesButConfigSetToLogBlank(array $original, array $modified): void
    {
        config(['change-logger.log_blank_changes' => true]);

        Log::shouldReceive('info')->with([
            'event' => 'Updated: [Tests\\Models\\SpyWithoutAnythingToLog]',
            'attributes' => [],
        ])->once();

        /** @var SpyWithoutAnythingToLog $model */
        $model = SpyWithoutAnythingToLog::create($original);
        $model->update($modified);
    }
    /**
     * ...
     *
     * @dataProvider changeProvider
     *
     * @param array $original
     * @param array $modified
     */
    public function testDatabaseChangeLogger(array $original, array $modified): void
    {
        // Set a static time
        $this->travelTo(Carbon::createFromFormat('Y-m-d H:i:s', '2020-10-17 12:00:00'));

        $changedAttributes = [];
        foreach ($modified as $attribute => $change) {
            $changedAttributes[$attribute] = [
                'original' => $original[$attribute],
                'new' => $change,
            ];
        }

        /** @var SpyWithDatabaseObserver $model */
        $model = SpyWithDatabaseObserver::create($original);
        $original = $model->getOriginal();

        $model->update($modified);

        $query = 'insert into "model_change_log" ' .
            '("model", "foreign_id", "original", "changes", "updated_at", "created_at") ' .
            'values (?, ?, ?, ?, ?, ?)';
        $this->assertQueryExists($query, [
            get_class($model),
            $model->getKey(),
            json_encode($original),
            json_encode($changedAttributes),
            '2020-10-17 12:00:00',
            '2020-10-17 12:00:00',
        ]);
    }

    /**
     * Ensure we have the correct amount of updates.
     */
    public function testGetChangeLog(): void
    {
        $model = SpyWithDatabaseObserver::create([
            'name' => 'Mario Mario',
            'alias' => 'Mario',
            'missions_complete' => 1,
            'active' => false,
        ]);

        $model->update([
            'active' => true,
        ]);

        $model->update([
            'missions_complete' => 2,
        ]);

        $model->update([
            'missions_complete' => 3,
        ]);

        $this->assertCount(3, $model->getChangeLog());
    }

    /**
     * Ensure we don't get updates from other models.
     */
    public function testGetChangeLogIntegrity(): void
    {
        $mario = SpyWithDatabaseObserver::create([
            'name' => 'Mario Mario',
            'alias' => 'Mario',
            'missions_complete' => 1,
            'active' => false,
        ]);

        $luigi = SpyWithDatabaseObserver::create([
            'name' => 'Luigi Mario',
            'alias' => 'Luigi',
            'missions_complete' => 1,
            'active' => true,
        ]);

        $luigi->update([
            'missions_complete' => 12,
        ]);

        $luigi->update([
            'active' => false,
        ]);


        $mario->update([
            'active' => true,
        ]);

        $mario->update([
            'missions_complete' => 2,
        ]);

        $mario->update([
            'missions_complete' => 3,
        ]);

        $this->assertCount(2, $luigi->getChangeLog());
        $this->assertCount(3, $mario->getChangeLog());
    }
}
