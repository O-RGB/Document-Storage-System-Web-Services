<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;




require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/FileProjectWebService');

require __DIR__ . '/app/User.php';
require __DIR__ . '/app/Folder.php';
require __DIR__ . '/app/upload.php';
require __DIR__ . '/app/Manager.php';
require __DIR__ . '/dbconnect.php';

$app->run();