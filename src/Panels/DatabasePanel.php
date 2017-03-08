<?php

namespace Recca0120\LaravelTracy\Panels;

use PDO;
use Exception;
use Recca0120\LaravelTracy\Contracts\IAjaxPanel;

class DatabasePanel extends AbstractSubscriablePanel implements IAjaxPanel
{
    /**
     * $queries.
     *
     * @var array
     */
    protected $queries = [];

    /**
     * $totalTime.
     *
     * @var float
     */
    protected $totalTime = 0.0;

    /**
     * $counter.
     *
     * @var int
     */
    protected $counter = 0;

    /**
     * subscribe.
     */
    protected function subscribe()
    {
        $events = $this->laravel['events'];
        if (version_compare($this->laravel->version(), 5.2, '>=') === true) {
            $events->listen('Illuminate\Database\Events\QueryExecuted', function ($event) {
                $this->logQuery(
                    $event->sql,
                    $event->bindings,
                    $event->time,
                    $event->connectionName,
                    $event->connection->getPdo()
                );
            });
        } else {
            $events->listen('illuminate.query', function ($sql, $bindings, $time, $connectionName) {
                $this->logQuery(
                    $sql,
                    $bindings,
                    $time,
                    $connectionName,
                    $this->laravel['db']->connection($connectionName)->getPdo()
                );
            });
        }
    }

    /**
     * logQuery.
     *
     * @param string $sql
     * @param array $bindings
     * @param int $time
     * @param string $name
     * @param PDO $pdo
     * @param string $driver
     * @return $this
     */
    public function logQuery($sql, $bindings = [], $time = 0, $name = null, PDO $pdo = null, $driver = 'mysql')
    {
        ++$this->counter;
        $this->totalTime += $time;
        $source = static::findSource();
        $editorLink = static::editorLink($source);
        $this->queries[] = [
            'sql' => $sql,
            'bindings' => $bindings,
            'time' => $time,
            'name' => $name,
            'pdo' => $pdo,
            'driver' => $driver,
            'source' => $source,
            'editorLink' => $editorLink,
            'hightlight' => null,
        ];

        return $this;
    }

    /**
     * getAttributes.
     *
     * @return array
     */
    protected function getAttributes()
    {
        $queries = [];
        foreach ($this->queries as $query) {
            $sql = $query['sql'];
            $bindings = $query['bindings'];
            $pdo = $query['pdo'];
            $driver = $query['driver'];
            $version = 0;

            $hightlight = Helper::hightlight($sql, $bindings, $pdo);
            $explains = [];
            $hints = [];
            if ($pdo instanceof PDO) {
                $driver = $this->getDatabaseDriver($pdo);
                if ($driver === 'mysql') {
                    $version = $this->getDatabaseVersion($pdo);
                    $explains = Helper::explain($pdo, $sql, $bindings);
                    $hints = Helper::performQueryAnalysis($sql, $version, $driver);
                }
            }

            $queries[] = array_merge($query, compact('hightlight', 'explains', 'hints', 'driver', 'version'));
        }

        return [
            'counter' => $this->counter,
            'totalTime' => $this->totalTime,
            'queries' => $queries,
        ];
    }

    /**
     * getDatabaseDriver.
     *
     * @param \PDO $pdo
     * @return string
     */
    protected function getDatabaseDriver(PDO $pdo)
    {
        try {
            $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        } catch (Exception $e) {
            $driver = null;
        }

        return $driver;
    }

    /**
     * getDatabaseVersion.
     *
     * @param \PDO $pdo
     * @return string
     */
    protected function getDatabaseVersion(PDO $pdo)
    {
        try {
            $version = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
        } catch (Exception $e) {
            $version = 0;
        }

        return $version;
    }
}
