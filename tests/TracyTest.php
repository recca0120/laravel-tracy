<?php

namespace Recca0120\LaravelTracy\Tests;

use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Tracy;

class TracyTest extends TestCase
{
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
        $databasePanel = $tracy->getPanel('database');
        $databasePanel->logQuery('select * from users');
        $databasePanel->logQuery('select * from news');
        $databasePanel->logQuery('select * from products');

        $this->assertIsString($databasePanel->getPanel());

        $authPanel = $tracy->getPanel('auth');
        $authPanel->setUserResolver(function () {
            return ['username' => 'foo'];
        });

        $this->assertIsString($authPanel->getPanel());
        $this->assertTrue($tracy->isEnabled());
    }
}
