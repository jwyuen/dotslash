<?php

require(dirname(__FILE__) . '/../bootstrap.php');


use Symfony\Component\Console\Application;

$scriptName = 'my-sample-script-suite';

$application = new Application();

// ExampleCommandClass would be a class that extends \Dotslash\BaseCommand class
$application->add(new ExampleCommandClass());

$application->setCatchExceptions(false);
$application->run();
