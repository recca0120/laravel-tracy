<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Application;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PDO;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Panels\DatabasePanel;
use Recca0120\LaravelTracy\Template;
use stdClass;

class DatabasePanelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testRender()
    {
        $laravel = m::spy(new Application());
        $laravel->expects('version')->andReturns(5.2);

        $events = m::spy(Dispatcher::class);
        $events
            ->expects('listen')
            ->with(QueryExecuted::class, m::on(function ($closure) {
                $connection = m::spy(Connection::class);
                $pdo = m::spy('PDO');
                $connection->expects('getPdo')->andReturns($pdo);
                $pdo->allows('quote')->andReturnUsing(function ($param) {
                    return addslashes($param);
                });
                $pdo->expects('getAttribute')->with(PDO::ATTR_DRIVER_NAME)->andReturns('mysql');

                $sql = 'SELECT * FROM users WHERE foo = ?';
                $bindings = ['bar'];
                $statement = m::mock('PDOStatement');
                $statement->expects('execute')->with($bindings);
                $statement->expects('fetchAll')->with(PDO::FETCH_CLASS);
                $pdo->expects('prepare')->with('EXPLAIN '.$sql)->andReturns($statement);

                $event = new stdClass;
                $event->sql = $sql;
                $event->bindings = $bindings;
                $event->time = 1;
                $event->connectionName = 'foo';
                $event->connection = $connection;

                $closure($event);

                return true;
            }));
        $laravel['events'] = $events;

        $template = m::spy(new Template());
        $panel = new DatabasePanel($template);
        $panel->setLaravel($laravel);

        $template->expects('setAttributes')->with(m::type('array'));
        $template->expects('render')->twice()->with(m::type('string'))->andReturns($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }

    public function testRenderAndLaravel50()
    {
        $laravel = m::spy(new Application());
        $laravel->expects('version')->andReturns(5.1);

        $events = m::spy(Dispatcher::class);
        $events->expects('listen')->with('illuminate.query', m::on(function ($closure) use ($laravel) {
            $sql = 'SELECT * FROM users WHERE foo = ?';
            $bindings = ['bar'];
            $time = 1;
            $connectionName = 'foo';
            $db = m::spy(DatabaseManager::class);
            $pdo = m::spy('PDO');
            $db->expects('connection')->with($connectionName)->andReturnSelf();
            $db->expects('getPdo')->andReturns($pdo);
            $pdo->allows('quote')->andReturnUsing(function ($param) {
                return addslashes($param);
            });

            $laravel->instance('db', $db);

            $closure($sql, $bindings, $time, $connectionName, $pdo);

            return true;
        }));
        $laravel['events'] = $events;

        $template = m::spy(new Template());
        $panel = new DatabasePanel($template);
        $panel->setLaravel($laravel);

        $template->expects('setAttributes')->with(m::type('array'));
        $template->expects('render')->twice()->with(m::type('string'))->andReturns($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }
}
