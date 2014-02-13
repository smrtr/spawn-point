<?php

/**
 * vendor/bin/spawn
 *
 * @package Smrtr\SpawnPoint
 * @author Joe Green
 */

require_once realpath(dirname(__FILE__).'/../../../autoload.php');

$app = new \Symfony\Component\Console\Application('spawn-point');
$app->add(new \Smrtr\SpawnPoint\SpawnCommand);
$app->run();
