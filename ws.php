<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use CSP\Report;

require 'vendor/autoload.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Report(dirname(__FILE__) . "/logs/")
        )
    ),
    8081
);

$server->run();