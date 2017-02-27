<?php

namespace Recca0120\LaravelTracy\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Tracy;

class TracyTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    /**
     * @runInSeparateProcess
     */
    public function testInstance()
    {
        $tracy = Tracy::instance([
            'email' => 'recca0120@gmail.com',
            'emailSnooze' => '3 days',
            'enabled' => true,
        ]);
        $tracy = Tracy::instance();
        $databasePanel = $tracy->getPanel('database');
        $databasePanel->logQuery('select * from users');
        $databasePanel->logQuery('select * from news');
        $databasePanel->logQuery('select * from products');

        $this->assertTrue(is_string($databasePanel->getPanel()));

        $authPanel = $tracy->getPanel('auth');
        $authPanel->setUserResolver(function () {
            return [
                'username' => 'foo',
            ];
        });

        $this->assertTrue(is_string($authPanel->getPanel()));

        ob_start();
        $tracy->dump(123);

        $this->assertTrue(is_string(ob_get_clean()));
    }
}
