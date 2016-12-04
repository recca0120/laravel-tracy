<?php

use Mockery as m;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Recca0120\LaravelTracy\Panels\DatabasePanel;

class DatabasePanelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_get_event_name()
    {
        $panel = new DatabasePanel();
        $eventName = class_exists('Illuminate\Database\Events\QueryExecuted') ? 'Illuminate\Database\Events\QueryExecuted' : 'illuminate.query';

        $this->assertSame($eventName, $panel->getEventName());
    }

    public function test_mysql()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $events = m::spy('Illuminate\Contracts\Event\Dispatcher');
        $connection = m::spy('Illuminate\Database\Connection');
        $pdo = m::spy('PDO');
        $statement = m::spy('PDOStatement');
        $eventName = 'Illuminate\Database\Events\QueryExecuted';

        $sql = 'SELECT * FROM users';
        $bindings = ['foo.sql'];
        $time = 1;
        $connectionName = 'foo.connection_name';
        $event = new stdClass();
        $event->sql = $sql;
        $event->bindings = $bindings;
        $event->time = $time;
        $event->connectionName = $connectionName;
        $event->connection = $connection;

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('offsetGet')->with('events')->andReturn($events);

        $connection
            ->shouldReceive('getPdo')->andReturn($pdo);

        $pdo
            ->shouldReceive('getAttribute')->with(PDO::ATTR_DRIVER_NAME)->andReturn('mysql')
            ->shouldReceive('prepare')->with('EXPLAIN '.$sql)->andReturn($statement);

        $statement
            ->shouldReceive('execute')->with($bindings)
            ->shouldReceive('fetchAll')->andReturn([['foo.explain']]);

        $events
            ->shouldReceive('listen')->with($eventName, m::type('Closure'))->andReturnUsing(function ($eventName, $closure) use ($event) {
                return $closure($event);
            });

        $panel = new DatabasePanel();
        $panel->setEventName($eventName);
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $panel->getAttributes();
        $this->assertTrue(is_string($panel->getTab()));
        $this->assertTrue(is_string($panel->getPanel()));

        $events->shouldHaveReceived('listen')->with($eventName, m::type('Closure'))->once();
        $connection->shouldHaveReceived('getPdo')->once();
        $pdo->shouldHaveReceived('getAttribute')->with(PDO::ATTR_DRIVER_NAME)->twice();
        $pdo->shouldHaveReceived('prepare')->with('EXPLAIN '.$sql)->twice();
        $statement->shouldHaveReceived('execute')->with($bindings)->twice();
        $statement->shouldHaveReceived('fetchAll')->with(PDO::FETCH_CLASS)->twice();
    }

    public function test_mysql_laravel50()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $app = m::spy('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $events = m::spy('Illuminate\Contracts\Event\Dispatcher');
        $db = m::spy('Illuminate\Database\DatabaseManager');
        $connection = m::spy('Illuminate\Database\Connection');
        $pdo = m::spy('PDO');
        $statement = m::spy('PDOStatement');
        $eventName = 'illuminate.query';

        $sql = 'SELECT * FROM users';
        $bindings = ['foo.sql'];
        $time = 1;
        $connectionName = 'foo.connection_name';

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $app
            ->shouldReceive('offsetGet')->with('events')->andReturn($events)
            ->shouldReceive('offsetGet')->with('db')->andReturn($db);

        $db
            ->shouldReceive('connection')->with($connectionName)->andReturn($connection);

        $connection
            ->shouldReceive('getPdo')->andReturn($pdo);

        $pdo
            ->shouldReceive('getAttribute')->with(PDO::ATTR_DRIVER_NAME)->andReturnUsing(function () {
                return new Exception();
            });

        $events
            ->shouldReceive('listen')->with($eventName, m::type('Closure'))->andReturnUsing(function ($eventName, $closure) use ($sql, $bindings, $time, $connectionName) {
                return $closure($sql, $bindings, $time, $connectionName);
            });

        $panel = new DatabasePanel();
        $panel->setEventName($eventName);
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $panel->getAttributes();
        $this->assertTrue(is_string($panel->getTab()));
        $this->assertTrue(is_string($panel->getPanel()));

        $events->shouldHaveReceived('listen')->with($eventName, m::type('Closure'))->once();
        $connection->shouldHaveReceived('getPdo')->once();
        $pdo->shouldHaveReceived('getAttribute')->with(PDO::ATTR_DRIVER_NAME)->twice();
    }

    public function test_prepare_bindings()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $now = new DateTime('now');
        $expected = [
            'sql' => 'SELECT * FROM users WHERE id IN ? AND name = ? AND created_at = ?',
            'bindings' => [[1, 2], 'foo', $now],
            'assert' => 'SELECT * FROM users WHERE id IN (1,2) AND name = \'foo\' AND created_at = \''.$now->format('Y-m-d H:i:s').'\'',
        ];

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame($expected['assert'], DatabasePanel::prepareBindings($expected['sql'], $expected['bindings']));
    }

    public function test_format_sql()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $sql = 'SELECT *, id, name, NOW() AS n FROM users WHERE name LIKE "%?%" AND id = (?) ORDER BY RAND(); /** **/ **foo**';
        $params = ['abc', 1];
        $connection = m::spy('PDO');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        DatabasePanel::formatSql($sql, $params, $connection);
    }

    public function test_perform_query_analysis()
    {
        $version = 5.0;
        $driver = 'mysql';

        $sql = 'SELECT * FROM users ORDER BY RAND()';
        DatabasePanel::performQueryAnalysis($sql, $version, $driver);

        $sql = 'SELECT * FROM users WHERE id != 1';
        DatabasePanel::performQueryAnalysis($sql, $version, $driver);

        $sql = 'SELECT * FROM users LIMIT 1';
        DatabasePanel::performQueryAnalysis($sql, $version, $driver);

        $sql = 'SELECT * FROM users WHERE name LIKE "foo%"';
        DatabasePanel::performQueryAnalysis($sql, $version, $driver);

        $sql = 'SELECT * FROM users WHERE id IN (SELECT user_id FROM roles)';
        DatabasePanel::performQueryAnalysis($sql, $version, $driver);
    }

    // public function test_mysql()
    // {
    //     /*
    //     |------------------------------------------------------------
    //     | Set
    //     |------------------------------------------------------------
    //     */

    //     $statement = m::mock('PDOStatement');
    //     $pdo = m::mock('PDO');
    //     $connection = m::mock('Illuminate\Database\Connection');
    //     $events = m::mock('Illuminate\Contracts\Event\Dispatcher');
    //     $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
    //     $db = m::mock('Illuminate\Database\DatabaseManager');
    //     $panel = new DatabasePanel();
    //     $eventName = $panel->getEventName();

    //     /*
    //     |------------------------------------------------------------
    //     | Expectation
    //     |------------------------------------------------------------
    //     */

    //     $statement
    //         ->shouldReceive('execute')
    //         ->shouldReceive('fetchAll');

    //     $pdo
    //         ->shouldReceive('getAttribute')->with(PDO::ATTR_DRIVER_NAME)->andReturn('mysql')
    //         ->shouldReceive('getAttribute')->with(PDO::ATTR_SERVER_VERSION)->andReturn(5.4)
    //         ->shouldReceive('prepare')->andReturn($statement);

    //     $connection
    //         ->shouldReceive('getName')->andReturn('mysql')
    //         ->shouldReceive('getPdo')->andReturn($pdo);

    //     $events
    //         ->shouldReceive('listen')->with($eventName, m::any())->andReturnUsing(function ($eventName, $closure) use ($connection) {
    //             $connectionName = ($eventName !== 'Illuminate\Database\Events\QueryExecuted') ? 'mysql' : $connection;
    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('SELECT DISTINCT * FROM `users` WHERE `id` != (?) ORDER BY RAND(); /** **/ **foo**', ['1'], 1.1, $connectionName)
    //             );

    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('SELECT * FROM `users` ORDER BY id', [], 1.1, $connectionName)
    //             );

    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('SELECT * FROM `users` LIMIT 1', [], 1.1, $connectionName)
    //             );

    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('SELECT * FROM `users` LIKE "%?%"', ['name'], 1.1, $connectionName)
    //             );

    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('SELECT * FROM `users` WHERE id IN (SELECT `id` FROM `users`)', [], 1.1, $connectionName)
    //             );

    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('UPDATE `users` SET `name` = ? WHERE `id` = ? AND `created_at` = ?', ['name', '1', new DateTime()], 1.1, $connectionName)
    //             );

    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('SELECT * FROM `users` WHERE `id` IN ?;', [['1', '2', 'test\'s']], 1.1, $connectionName)
    //             );
    //         })->once();

    //     $app
    //         ->shouldReceive('offsetGet')->with('events')->once()->andReturn($events)
    //         ->shouldReceive('offsetGet')->with('db')->andReturn($db);

    //     $db->shouldReceive('connection')->with('mysql')->andReturn($connection);

    //     $panel->setLaravel($app);

    //     /*
    //     |------------------------------------------------------------
    //     | Assertion
    //     |------------------------------------------------------------
    //     */

    //     $panel->getTab();
    //     $panel->getPanel();
    // }

    // public function test_mssql()
    // {
    //     /*
    //     |------------------------------------------------------------
    //     | Set
    //     |------------------------------------------------------------
    //     */

    //     $pdo = m::mock('PDO');
    //     $connection = m::mock('Illuminate\Database\Connection');
    //     $events = m::mock('Illuminate\Contracts\Event\Dispatcher');
    //     $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
    //     $panel = new DatabasePanel();
    //     $panel->setEventName('Illuminate\Database\Events\QueryExecuted');

    //     /*
    //     |------------------------------------------------------------
    //     | Expectation
    //     |------------------------------------------------------------
    //     */

    //     $pdo = m::mock('PDO');
    //     $connection
    //         ->shouldReceive('getName')->andReturn('sqlsrv')
    //         ->shouldReceive('getPdo')->andReturn($pdo);

    //     $events
    //         ->shouldReceive('listen')->with('Illuminate\Database\Events\QueryExecuted', m::any())->andReturnUsing(function ($eventName, $closure) use ($connection) {
    //             $connectionName = ($eventName !== 'Illuminate\Database\Events\QueryExecuted') ? 'mysql' : $connection;
    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('SELECT DISTINCT * FROM `users` WHERE `id` != (?) ORDER BY RAND(); /** **/ **foo**', ['1'], 1.1, $connectionName)
    //             );

    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('SELECT * FROM `users` ORDER BY id', [], 1.1, $connectionName)
    //             );

    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('SELECT * FROM `users` LIMIT 1', [], 1.1, $connectionName)
    //             );

    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('SELECT * FROM `users` LIKE "%?%"', ['name'], 1.1, $connectionName)
    //             );

    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('SELECT * FROM `users` WHERE id IN (SELECT `id` FROM `users`)', [], 1.1, $connectionName)
    //             );

    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('UPDATE `users` SET `name` = ? WHERE `id` = ? AND `created_at` = ?', ['name', '1', new DateTime()], 1.1, $connectionName)
    //             );

    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('SELECT * FROM `users` WHERE `id` IN ?;', [['1', '2', 'test\'s']], 1.1, $connectionName)
    //             );
    //         });

    //     $app->shouldReceive('offsetGet')->with('events')->andReturn($events);

    //     $panel->setLaravel($app);

    //     /*
    //     |------------------------------------------------------------
    //     | Assertion
    //     |------------------------------------------------------------
    //     */

    //     $panel->getTab();
    //     $panel->getPanel();
    // }

    // public function test_mysql_laravel50()
    // {
    //     /*
    //     |------------------------------------------------------------
    //     | Set
    //     |------------------------------------------------------------
    //     */

    //     $statement = m::mock('PDOStatement');
    //     $pdo = m::mock('PDO');
    //     $connection = m::mock('Illuminate\Database\Connection');
    //     $events = m::mock('Illuminate\Contracts\Event\Dispatcher');
    //     $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
    //     $db = m::mock('Illuminate\Database\DatabaseManager');
    //     $panel = new DatabasePanel();
    //     $panel->setEventName('illuminate.query');

    //     /*
    //     |------------------------------------------------------------
    //     | Expectation
    //     |------------------------------------------------------------
    //     */

    //     $statement
    //         ->shouldReceive('execute')
    //         ->shouldReceive('fetchAll');

    //     $pdo
    //         ->shouldReceive('getAttribute')->with(PDO::ATTR_DRIVER_NAME)->andReturn('mysql')
    //         ->shouldReceive('getAttribute')->with(PDO::ATTR_SERVER_VERSION)->andReturn(5.4)
    //         ->shouldReceive('prepare')->andReturn($statement);

    //     $connection
    //         ->shouldReceive('getName')->andReturn('mysql')
    //         ->shouldReceive('getPdo')->andReturn($pdo);

    //     $events
    //         ->shouldReceive('listen')->with('illuminate.query', m::any())->andReturnUsing(function ($eventName, $closure) {
    //             $connectionName = ($eventName !== 'Illuminate\Database\Events\QueryExecuted') ? 'mysql' : $connection;
    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('SELECT DISTINCT * FROM `users` WHERE `id` != (?) ORDER BY RAND(); /** **/ **foo**', ['1'], 1.1, $connectionName)
    //             );

    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('SELECT * FROM `users` ORDER BY id', [], 1.1, $connectionName)
    //             );

    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('SELECT * FROM `users` LIMIT 1', [], 1.1, $connectionName)
    //             );

    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('SELECT * FROM `users` LIKE "%?%"', ['name'], 1.1, $connectionName)
    //             );

    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('SELECT * FROM `users` WHERE id IN (SELECT `id` FROM `users`)', [], 1.1, $connectionName)
    //             );

    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('UPDATE `users` SET `name` = ? WHERE `id` = ? AND `created_at` = ?', ['name', '1', new DateTime()], 1.1, $connectionName)
    //             );

    //             call_user_func_array(
    //                 $closure,
    //                 $this->queryExecuted('SELECT * FROM `users` WHERE `id` IN ?;', [['1', '2', 'test\'s']], 1.1, $connectionName)
    //             );
    //         })->once();

    //     $app
    //         ->shouldReceive('offsetGet')->with('events')->once()->andReturn($events)
    //         ->shouldReceive('offsetGet')->with('db')->andReturn($db);

    //     $db->shouldReceive('connection')->with('mysql')->andReturn($connection);

    //     $panel->setLaravel($app);

    //     /*
    //     |------------------------------------------------------------
    //     | Assertion
    //     |------------------------------------------------------------
    //     */

    //     $panel->getTab();
    //     $panel->getPanel();
    // }

    // public function test_get_event_name()
    // {
    //     $panel = new DatabasePanel();
    //     $eventName = class_exists('Illuminate\Database\Events\QueryExecuted') ? 'Illuminate\Database\Events\QueryExecuted' : 'illuminate.query';
    //     $this->assertSame($eventName, $panel->getEventName());
    // }

    // protected function queryExecuted($sql, $bindings, $time, $connection)
    // {
    //     if (is_string($connection) == true) {
    //         return [$sql, $bindings, $time, $connection];
    //     }

    //     if (class_exists('Illuminate\Database\Events\QueryExecuted') === true) {
    //         $event = new Illuminate\Database\Events\QueryExecuted($sql, $bindings, $time, $connection);
    //     } else {
    //         $event = new stdClass();
    //         $event->sql = $sql;
    //         $event->bindings = $bindings;
    //         $event->time = $time;
    //         $event->connectionName = 'mysql';
    //         $event->connection = $connection;
    //     }

    //     return [$event];
    // }
}
