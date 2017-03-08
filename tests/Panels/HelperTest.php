<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use PDO;
use DateTime;
use stdClass;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Connection;
use Recca0120\LaravelTracy\Panels\Helper;

class HelperTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testHighlight()
    {
        $pdo = m::mock('PDO');
        $pdo->shouldReceive('quote')->andReturnUsing(function ($param) {
            return addslashes($param);
        });

        $now = new DateTime('now');
        $fp = fopen(__FILE__, 'r');
        $this->assertSame(
            'SELECT *, id, name, NOW() AS n FROM users WHERE name LIKE "%foo%" AND id = (1, 2) AND created_at = \''.$now->format('Y-m-d H:i:s').'\' AND resource = &lt;stream resource&gt; ORDER BY RAND(); /** **/ **foo**',
            preg_replace("/\n/", '',
                strip_tags(
                    Helper::hightlight(
                        'SELECT *, id, name, NOW() AS n FROM users WHERE name LIKE "%?%" AND id = ? AND created_at = ? AND resource = ? ORDER BY RAND(); /** **/ **foo**',
                        ['foo', [1, 2], $now, $fp],
                        $pdo
                    )
                )
            )
        );
        fclose($fp);
    }

    public function testPerformQueryAnalysis()
    {
        $version = 5.0;
        $driver = 'mysql';

        $sql = 'SELECT * FROM users ORDER BY RAND()';
        Helper::performQueryAnalysis($sql, $version, $driver);

        $sql = 'SELECT * FROM users WHERE id != 1';
        Helper::performQueryAnalysis($sql, $version, $driver);

        $sql = 'SELECT * FROM users LIMIT 1';
        Helper::performQueryAnalysis($sql, $version, $driver);

        $sql = 'SELECT * FROM users WHERE name LIKE "foo%"';
        Helper::performQueryAnalysis($sql, $version, $driver);

        $sql = 'SELECT * FROM users WHERE name LIKE "%foo%"';
        Helper::performQueryAnalysis($sql, $version, $driver);

        $sql = 'SELECT * FROM users WHERE id IN (SELECT user_id FROM roles)';
        Helper::performQueryAnalysis($sql, $version, $driver);
    }
}
