<?php

use Mockery as m;
use Recca0120\LaravelTracy\Panels\DatabasePanel;

class DatabasePanelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_mysql()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $statement = m::mock('PDOStatement');
        $pdo = m::mock('PDO');
        $connection = m::mock('Illuminate\Database\Connection');
        $events = m::mock('Illuminate\Contracts\Events\Dispatcher');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $db = m::mock('Illuminate\Database\DatabaseManager');
        $panel = new DatabasePanel();
        $eventName = $panel->getEventName();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $statement
            ->shouldReceive('execute')
            ->shouldReceive('fetchAll');

        $pdo
            ->shouldReceive('getAttribute')->with(PDO::ATTR_DRIVER_NAME)->andReturn('mysql')
            ->shouldReceive('getAttribute')->with(PDO::ATTR_SERVER_VERSION)->andReturn(5.4)
            ->shouldReceive('prepare')->andReturn($statement);

        $connection
            ->shouldReceive('getName')->andReturn('mysql')
            ->shouldReceive('getPdo')->andReturn($pdo);

        $events
            ->shouldReceive('listen')->with($eventName, m::any())->andReturnUsing(function ($eventName, $closure) use ($connection) {
                $connectionName = ($eventName !== 'Illuminate\Database\Events\QueryExecuted') ? 'mysql' : $connection;
                call_user_func_array(
                    $closure,
                    $this->queryExecuted('SELECT DISTINCT * FROM `users` WHERE `id` != (?) ORDER BY RAND(); /** **/ **foo**', ['1'], 1.1, $connectionName)
                );

                call_user_func_array(
                    $closure,
                    $this->queryExecuted('SELECT * FROM `users` ORDER BY id', [], 1.1, $connectionName)
                );

                call_user_func_array(
                    $closure,
                    $this->queryExecuted('SELECT * FROM `users` LIMIT 1', [], 1.1, $connectionName)
                );

                call_user_func_array(
                    $closure,
                    $this->queryExecuted('SELECT * FROM `users` LIKE "%?%"', ['name'], 1.1, $connectionName)
                );

                call_user_func_array(
                    $closure,
                    $this->queryExecuted('SELECT * FROM `users` WHERE id IN (SELECT `id` FROM `users`)', [], 1.1, $connectionName)
                );

                call_user_func_array(
                    $closure,
                    $this->queryExecuted('UPDATE `users` SET `name` = ? WHERE `id` = ? AND `created_at` = ?', ['name', '1', new DateTime()], 1.1, $connectionName)
                );

                call_user_func_array(
                    $closure,
                    $this->queryExecuted('SELECT * FROM `users` WHERE `id` IN ?;', [['1', '2', 'test\'s']], 1.1, $connectionName)
                );
            })->once();

        $app
            ->shouldReceive('offsetGet')->with('db')->andReturn($db)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events);

        $db->shouldReceive('connection')->andReturn($connection);

        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $panel->getTab();
        $panel->getPanel();
    }

    public function test_mssql()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $pdo = m::mock('PDO');
        $connection = m::mock('Illuminate\Database\Connection');
        $events = m::mock('Illuminate\Contracts\Events\Dispatcher');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $panel = new DatabasePanel();
        $panel->setEventName('Illuminate\Database\Events\QueryExecuted');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $pdo = m::mock('PDO');
        $connection
            ->shouldReceive('getName')->andReturn('sqlsrv')
            ->shouldReceive('getPdo')->andReturn($pdo);

        $events
            ->shouldReceive('listen')->with('Illuminate\Database\Events\QueryExecuted', m::any())->andReturnUsing(function ($eventName, $closure) use ($connection) {
                $connectionName = ($eventName !== 'Illuminate\Database\Events\QueryExecuted') ? 'mysql' : $connection;
                call_user_func_array(
                    $closure,
                    $this->queryExecuted('SELECT DISTINCT * FROM `users` WHERE `id` != ? ORDER BY RAND(); /** **/', ['1'], 1.1, $connectionName)
                );

                call_user_func_array(
                    $closure,
                    $this->queryExecuted('SELECT * FROM `users` ORDER BY id', [], 1.1, $connectionName)
                );

                call_user_func_array(
                    $closure,
                    $this->queryExecuted('SELECT * FROM `users` LIMIT 1', [], 1.1, $connectionName)
                );

                call_user_func_array(
                    $closure,
                    $this->queryExecuted('SELECT * FROM `users` LIKE "%?%"', ['name'], 1.1, $connectionName)
                );

                call_user_func_array(
                    $closure,
                    $this->queryExecuted('SELECT * FROM `users` WHERE id IN (SELECT `id` FROM `users`)', [], 1.1, $connectionName)
                );

                call_user_func_array(
                    $closure,
                    $this->queryExecuted('UPDATE `users` SET `name` = ? WHERE `id` = ? AND `created_at` = ?', ['name', '1', new DateTime()], 1.1, $connectionName)
                );
            });

        $app->shouldReceive('offsetGet')->with('events')->andReturn($events);

        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $panel->getTab();
        $panel->getPanel();
    }

    public function test_mysql_laravel50()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $statement = m::mock('PDOStatement');
        $pdo = m::mock('PDO');
        $connection = m::mock('Illuminate\Database\Connection');
        $events = m::mock('Illuminate\Contracts\Events\Dispatcher');
        $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $db = m::mock('Illuminate\Database\DatabaseManager');
        $panel = new DatabasePanel();
        $panel->setEventName('illuminate.query');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $statement
            ->shouldReceive('execute')
            ->shouldReceive('fetchAll');

        $pdo
            ->shouldReceive('getAttribute')->with(PDO::ATTR_DRIVER_NAME)->andReturn('mysql')
            ->shouldReceive('getAttribute')->with(PDO::ATTR_SERVER_VERSION)->andReturn(5.4)
            ->shouldReceive('prepare')->andReturn($statement);

        $connection
            ->shouldReceive('getName')->andReturn('mysql')
            ->shouldReceive('getPdo')->andReturn($pdo);

        $events
            ->shouldReceive('listen')->with('illuminate.query', m::any())->andReturnUsing(function ($eventName, $closure) use ($connection) {
                $connectionName = ($eventName !== 'Illuminate\Database\Events\QueryExecuted') ? 'mysql' : $connection;
                call_user_func_array(
                    $closure,
                    $this->queryExecuted('SELECT DISTINCT * FROM `users` WHERE `id` != (?) ORDER BY RAND(); /** **/ **foo**', ['1'], 1.1, $connectionName)
                );

                call_user_func_array(
                    $closure,
                    $this->queryExecuted('SELECT * FROM `users` ORDER BY id', [], 1.1, $connectionName)
                );

                call_user_func_array(
                    $closure,
                    $this->queryExecuted('SELECT * FROM `users` LIMIT 1', [], 1.1, $connectionName)
                );

                call_user_func_array(
                    $closure,
                    $this->queryExecuted('SELECT * FROM `users` LIKE "%?%"', ['name'], 1.1, $connectionName)
                );

                call_user_func_array(
                    $closure,
                    $this->queryExecuted('SELECT * FROM `users` WHERE id IN (SELECT `id` FROM `users`)', [], 1.1, $connectionName)
                );

                call_user_func_array(
                    $closure,
                    $this->queryExecuted('UPDATE `users` SET `name` = ? WHERE `id` = ? AND `created_at` = ?', ['name', '1', new DateTime()], 1.1, $connectionName)
                );

                call_user_func_array(
                    $closure,
                    $this->queryExecuted('SELECT * FROM `users` WHERE `id` IN ?;', [['1', '2', 'test\'s']], 1.1, $connectionName)
                );
            });

        $app
            ->shouldReceive('offsetGet')->with('db')->andReturn($db)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events);

        $db->shouldReceive('connection')->andReturn($connection);

        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $panel->getTab();
        $panel->getPanel();
    }

    public function test_get_event_name()
    {
        $panel = new DatabasePanel();
        $eventName = class_exists('Illuminate\Database\Events\QueryExecuted') ? 'Illuminate\Database\Events\QueryExecuted' : 'illuminate.query';
        $this->assertSame($eventName, $panel->getEventName());
    }

    protected function queryExecuted($sql, $bindings, $time, $connection)
    {
        if (is_string($connection) == true) {
            return [$sql, $bindings, $time, $connection];
        }

        if (class_exists('Illuminate\Database\Events\QueryExecuted') === true) {
            $event = new Illuminate\Database\Events\QueryExecuted($sql, $bindings, $time, $connection);
        } else {
            $event = new stdClass;
            $event->sql = $sql;
            $event->bindings = $bindings;
            $event->time = $time;
            $event->connectionName = 'mysql';
            $event->connection = $connection;
        }

        return [$event];
    }
}
