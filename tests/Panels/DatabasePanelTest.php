<?php

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Mockery as m;
use Recca0120\LaravelTracy\Panels\DatabasePanel;

class DatabasePanelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_database_panel_mysql_52()
    {
        $statement = m::mock(PDOStatement::class)
            ->shouldReceive('execute')
            ->shouldReceive('fetchAll')
            ->mock();

        $pdo = m::mock(PDO::class)
            ->shouldReceive('getAttribute')->with(PDO::ATTR_DRIVER_NAME)->andReturn('mysql')
            ->shouldReceive('getAttribute')->with(PDO::ATTR_SERVER_VERSION)->andReturn(5.4)
            ->shouldReceive('prepare')->andReturn($statement)
            ->mock();

        $connection = m::mock(Connection::class)
            ->shouldReceive('getName')->andReturn('mysql')
            ->shouldReceive('getPdo')->andReturn($pdo)
            ->mock();

        $events = m::mock(DispatcherContract::class)
            ->shouldReceive('listen')->with(QueryExecuted::class, m::any())->andReturnUsing(function ($eventName, $closure) use ($connection) {
                $queryExecuted = new QueryExecuted('SELECT DISTINCT * FROM `users` WHERE `id` != (?) ORDER BY RAND(); /** **/', ['1'], 1.1, $connection);
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
            })
            ->mock();

        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events)
            ->mock();

        $panel = new DatabasePanel();
        $panel->setLaravel($app);

        $panel->getTab();
        $panel->getPanel();
    }

    public function test_database_panel_mysql_51()
    {
        $statement = m::mock(PDOStatement::class)
            ->shouldReceive('execute')
            ->shouldReceive('fetchAll')
            ->mock();

        $pdo = m::mock(PDO::class)
            ->shouldReceive('getAttribute')->with(PDO::ATTR_DRIVER_NAME)->andReturn('mysql')
            ->shouldReceive('getAttribute')->with(PDO::ATTR_SERVER_VERSION)->andReturn(5.6)
            ->shouldReceive('prepare')->andReturn($statement)
            ->mock();

        $connection = m::mock(Connection::class)
            ->shouldReceive('getName')->andReturn('mysql')
            ->shouldReceive('getPdo')->andReturn($pdo)
            ->shouldReceive('connection')->andReturnSelf()
            ->mock();

        $events = m::mock(DispatcherContract::class)
            ->shouldReceive('listen')->with('illuminate.query', m::any())->andReturnUsing(function ($eventName, $closure) use ($connection) {
                $closure('SELECT DISTINCT * FROM `users` WHERE `id` != ? ORDER BY RAND(); /** **/', ['1'], 1.1, 'mysql');

                $closure('SELECT * FROM `users` ORDER BY id', [], 1.1, 'mysql');

                $closure('SELECT * FROM `users` LIMIT 1', [], 1.1, 'mysql');

                $closure('SELECT * FROM `users` LIKE "%?%"', ['name'], 1.1, 'mysql');

                $closure('SELECT * FROM `users` WHERE id IN (SELECT `id` FROM `users`)', [], 1.1, 'mysql');

                $closure('UPDATE `users` SET `name` = ? WHERE `id` = ? AND `created_at` = ?', ['name', '1', new DateTime()], 1.1, 'mysql');

                $closure('select * from users where id = ?', ['1'], 1.1, 'mysql');
            })
            ->mock();

        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('version')->andReturn(5.1)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events)
            ->shouldReceive('offsetGet')->with('db')->andReturn($connection)
            ->mock();

        $panel = new DatabasePanel();
        $panel->setLaravel($app);

        $panel->getTab();
        $panel->getPanel();
    }

    public function test_database_panel_mssql_52()
    {
        $pdo = m::mock(PDO::class);

        $connection = m::mock(Connection::class)
            ->shouldReceive('getName')->andReturn('sqlsrv')
            ->shouldReceive('getPdo')->andReturn($pdo)
            ->mock();

        $events = m::mock(DispatcherContract::class)
            ->shouldReceive('listen')->with(QueryExecuted::class, m::any())->andReturnUsing(function ($eventName, $closure) use ($connection) {
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
            })
            ->mock();

        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('version')->andReturn(5.2)
            ->shouldReceive('offsetGet')->with('events')->andReturn($events)
            ->mock();

        $panel = new DatabasePanel();
        $panel->setLaravel($app);

        $panel->getTab();
        $panel->getPanel();
    }
}
