<?php

use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Mockery as m;
use Recca0120\LaravelTracy\Panels\DatabasePanel;

class DatabasePanelTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (class_exists('Illuminate\Database\Events\QueryExecuted') === false) {
            $this->markTestSkipped(
              'this is laravel 5.2 testing'
            );
        }
    }

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
        $panel = new DatabasePanel();

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

        if (class_exists('Illuminate\Database\Events\QueryExecuted') === false) {
            return;
        }
        $events
            ->shouldReceive('listen')->with('Illuminate\Database\Events\QueryExecuted', m::any())->andReturnUsing(function ($eventName, $closure) use ($connection) {
                $queryExecuted = new QueryExecuted('SELECT DISTINCT * FROM `users` WHERE `id` != (?) ORDER BY RAND(); /** **/ **foo**', ['1'], 1.1, $connection);
                $closure($queryExecuted);

                $queryExecuted = new QueryExecuted('SELECT * FROM `users` ORDER BY id', [], 1.1, $connection);
                $closure($queryExecuted);

                $queryExecuted = new QueryExecuted('SELECT * FROM `users` LIMIT 1', [], 1.1, $connection);
                $closure($queryExecuted);

                $queryExecuted = new QueryExecuted('SELECT * FROM `users` LIKE "%?%"', ['name'], 1.1, $connection);
                $closure($queryExecuted);

                $queryExecuted = new QueryExecuted('SELECT * FROM `users` WHERE id IN (SELECT `id` FROM `users`)', [], 1.1, $connection);
                $closure($queryExecuted);

                $queryExecuted = new QueryExecuted('UPDATE `users` SET `name` = ? WHERE `id` = ? AND `created_at` = ?', ['name', '1', new DateTime()], 1.1, $connection);
                $closure($queryExecuted);

                $queryExecuted = new QueryExecuted('SELECT DISTINCT * FROM `users` WHERE `id` IN ? ORDER BY RAND(); /** **/', [['1', '2', 'test\'s']], 1.1, $connection);
                $closure($queryExecuted);
            });

        $app
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events);

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
                $queryExecuted = new QueryExecuted('SELECT DISTINCT * FROM `users` WHERE `id` != ? ORDER BY RAND(); /** **/', ['1'], 1.1, $connection);
                $closure($queryExecuted);

                $queryExecuted = new QueryExecuted('SELECT * FROM `users` ORDER BY id', [], 1.1, $connection);
                $closure($queryExecuted);

                $queryExecuted = new QueryExecuted('SELECT * FROM `users` LIMIT 1', [], 1.1, $connection);
                $closure($queryExecuted);

                $queryExecuted = new QueryExecuted('SELECT * FROM `users` LIKE "%?%"', ['name'], 1.1, $connection);
                $closure($queryExecuted);

                $queryExecuted = new QueryExecuted('SELECT * FROM `users` WHERE id IN (SELECT `id` FROM `users`)', [], 1.1, $connection);
                $closure($queryExecuted);

                $queryExecuted = new QueryExecuted('UPDATE `users` SET `name` = ? WHERE `id` = ? AND `created_at` = ?', ['name', '1', new DateTime()], 1.1, $connection);
                $closure($queryExecuted);
            });
        $app
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events);
        $panel->setLaravel($app);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $panel->getTab();
        $panel->getPanel();
    }
}
