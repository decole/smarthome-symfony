<?php

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;

//require dirname(__DIR__).'/vendor/autoload.php';

//if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
//    require dirname(__DIR__).'/config/bootstrap.php';
//} elseif (method_exists(Dotenv::class, 'bootEnv')) {
//    (new Dotenv())->loadEnv(dirname(__DIR__) . '/.env', '.', 'test');
//}

$dotenv = (new Dotenv());
$dotenv->usePutenv(true);
$dotenv->loadEnv(dirname(__DIR__) . '/.env', '.', 'test');


Debug::enable();

DG\BypassFinals::enable();