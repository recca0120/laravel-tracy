<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use PDO;
use DateTime;
use stdClass;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Connection;
use Recca0120\LaravelTracy\Panels\DatabasePanel;

class DatabasePanelTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testRender()
    {
        $panel = new DatabasePanel();
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $laravel->shouldReceive('offsetGet')->once()->with('events')->andReturn(
            $events = m::mock('Illuminate\Contracts\Event\Dispatcher')
        );
        $laravel->shouldReceive('version')->once()->andReturn(5.2);

        $events->shouldReceive('listen')->once()->with('Illuminate\Database\Events\QueryExecuted', m::on(function ($closure) {
            $event = new stdClass;
            $event->sql = $sql = 'SELECT * FROM users WHERE foo = ?';
            $event->bindings = $bindings = ['bar'];
            $event->time = 1;
            $event->connectionName = 'foo';
            $event->connection = $connection = m::mock('Illuminate\Database\Connection');
            $connection->shouldReceive('getPdo')->once()->andReturn(
                $pdo = m::mock('PDO')
            );

            $pdo->shouldReceive('getAttribute')->once()->with(PDO::ATTR_DRIVER_NAME)->andReturn('mysql');
            $pdo->shouldReceive('prepare')->once()->with('EXPLAIN '.$sql)->andReturn(
                $statement = m::mock('PDOStatement')
            );
            $statement->shouldReceive('execute')->once()->with($bindings);
            $statement->shouldReceive('fetchAll')->once()->with(PDO::FETCH_CLASS);

            $closure($event);

            return true;
        }));
        $panel->setLaravel($laravel);
        $panel->getTab();
        $panel->getPanel();
    }

    public function testRenderAndLaravel50()
    {
        $panel = new DatabasePanel();
        $laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess');
        $laravel->shouldReceive('offsetGet')->once()->with('events')->andReturn(
            $events = m::mock('Illuminate\Contracts\Event\Dispatcher')
        );
        $laravel->shouldReceive('version')->once()->andReturn(5.1);

        $events->shouldReceive('listen')->once()->with('illuminate.query', m::on(function ($closure) use ($laravel) {
            $sql = 'SELECT * FROM users WHERE foo = ?';
            $bindings = ['bar'];
            $time = 1;
            $connectionName = 'foo';

            $laravel->shouldReceive('offsetGet')->once()->with('db')->andReturn(
                $db = m::spy('Illuminate\Database\DatabaseManager')
            );
            $db->shouldReceive('connection')->once()->with($connectionName)->andReturnSelf()
                ->shouldReceive('getPdo')->once()->andReturn(
                    $pdo = m::mock('PDO')
                );

            $closure($sql, $bindings, $time, $connectionName, $pdo);

            return true;
        }));
        $panel->setLaravel($laravel);
        $panel->getTab();
        $panel->getPanel();
    }

    public function testPrepareBindings()
    {
        $now = new DateTime('now');
        $this->assertSame(
            'SELECT * FROM users WHERE id IN (1,2) AND name = \'foo\' AND created_at = \''.$now->format('Y-m-d H:i:s').'\'',
            DatabasePanel::prepareBindings(
                'SELECT * FROM users WHERE id IN ? AND name = ? AND created_at = ?',
                [[1, 2], 'foo', $now]
            )
        );
    }

    public function testFormatSQL()
    {
        $pdo = m::mock('PDO');
        $pdo->shouldReceive('quote')->andReturnUsing(function ($param) {
            return addslashes($param);
        });
        DatabasePanel::formatSql(
            'SELECT *, id, name, NOW() AS n FROM users WHERE name LIKE "%?%" AND id = (?) ORDER BY RAND(); /** **/ **foo**',
            ['foo', 1],
            $pdo
        );
    }

    public function testPerformQueryAnalysis()
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

        $sql = 'SELECT * FROM users WHERE name LIKE "%foo%"';
        DatabasePanel::performQueryAnalysis($sql, $version, $driver);

        $sql = 'SELECT * FROM users WHERE id IN (SELECT user_id FROM roles)';
        DatabasePanel::performQueryAnalysis($sql, $version, $driver);
    }
}
