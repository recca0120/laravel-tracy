<?php

require __DIR__.'/../vendor/autoload.php';

use Recca0120\LaravelTracy\Tracy;

$tracy = Tracy::enable();
$databasePanel = $tracy->getPanel('database');
$databasePanel->logQuery('select * from users');
$databasePanel->logQuery('select * from products');
echo $barpanel = $tracy->renderPanel();
