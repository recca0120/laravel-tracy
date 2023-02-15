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
        $events->expects('listen')->with(QueryExecuted::class, m::on(function ($closure) {
            $sql = 'SELECT * FROM users WHERE foo = ?';
            $bindings = ['bar'];

            $pdo = $this->givenExplains($sql, $bindings, [[]]);

            $connection = m::spy(Connection::class);
            $connection->expects('getPdo')->andReturns($pdo);

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
            $pdo = $this->givenExplains($sql, $bindings, []);

            $db = m::spy(DatabaseManager::class);
            $db->expects('connection')->with($connectionName)->andReturnSelf();
            $db->expects('getPdo')->andReturns($pdo);

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

    /**
     * @param  string  $sql
     * @param  array  $bindings
     * @param $results
     * @return PDO
     */
    private function givenExplains($sql, array $bindings, $results)
    {
        $pdo = m::spy('PDO');
        $pdo->allows('quote')->andReturnUsing(function ($param) {
            return addslashes($param);
        });
        $pdo->expects('getAttribute')->with(PDO::ATTR_DRIVER_NAME)->andReturns('mysql');

        $statement = m::mock('PDOStatement');
        $statement->expects('execute')->with($bindings);
        $statement->expects('fetchAll')->with(PDO::FETCH_CLASS)->andReturn($results);
        $pdo->expects('prepare')->with('EXPLAIN '.$sql)->andReturns($statement);

        return $pdo;
    }

    public function testRenderForPHP81()
    {
        $laravel = m::spy(new Application());
        $laravel->expects('version')->andReturns(5.2);

        $events = m::spy(Dispatcher::class);
        $events->expects('listen')->with(QueryExecuted::class, m::on(function ($closure) {
            $sql = 'SELECT * FROM users WHERE foo = ?';
            $bindings = ['bar'];

            $pdo = $this->givenExplains($sql, $bindings, json_decode('[{"id":1,"select_type":"SIMPLE","table":"users","partitions":null,"type":"ALL","possible_keys":null,"key":null,"key_len":null,"ref":null,"rows":3,"filtered":100,"Extra":null}]'));

            $connection = m::spy(Connection::class);
            $connection->expects('getPdo')->andReturns($pdo);

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

        $content = $panel->getPanel();
        $this->assertTrue(strpos($content, 'Deprecated') === false, $content);
    }
}
