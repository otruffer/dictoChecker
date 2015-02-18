#!/usr/bin/env php
<?php
require_once __DIR__.'/autoloader.php';

use Symfony\Component\Console\Application;
use DependChecker\DependCommand;

$application = new Application();
$application->add(new DependCommand());
$application->run();