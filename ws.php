<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require 'vendor/autoload.php';


class Reporter implements MessageComponentInterface 
{
    protected $clients;

    function __construct($logPath) 
    {
        $this->logPath = $logPath;
        $this->clients = new \SplObjectStorage;
        $this->backlog = [];
    }

    function onOpen(ConnectionInterface $conn) 
    {
        $this->clients->attach($conn);
        $query = $conn->httpRequest->getUri()->getQuery();
        parse_str($query, $data);

        if (!isset($data['id'])) {
            return;
        }

        $conn->docId = $data['id'];

        if (!isset($this->backlog[$data['id']])) {
            sleep(2);
        }
        
        if (!isset($this->backlog[$data['id']])) {
            return;
        }

        array_map(function($msg) use ($conn) {
            // @todo parse msg & add data using page hash
            $conn->send($msg);
        }, array_key_exists($data['id'], $this->backlog) ? $this->backlog[$data['id']]: []);

        $this->clients->attach($conn);

        unset($this->backlog[$data['id']]);

        echo "New browser connection! ({$conn->resourceId})\n";
    }

    function onMessage(ConnectionInterface $from, $msg) 
    {      
        // Ignore non-json messages ....
        if (strpos($msg, "{") !== 0) {
            return;
        }
        $data = json_decode($msg, true);

        if (!isset($data['doc_id'])) {
            return;
        }

        $docId = $data['doc_id'];
        if (!isset($this->backlog[$docId])) {
            $this->backlog[$docId] = [];
        }

        $this->backlog[$docId][] = $msg;

        foreach ($this->clients as $client) {
            if (!isset($client->docId)) {
                continue;
            }
            
            if ($docId !== $client->docId) {
                continue;
            }

            $client->send($msg);
        }
    }

    function onClose(ConnectionInterface $conn) 
    {
        $this->clients->detach($conn);
    }

    function onError(ConnectionInterface $conn, \Exception $e) 
    {
        $conn->close();
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Reporter(dirname(__FILE__) . "/logs/")
        )
    ),
    8110
);

echo "[i] Websockets on port 8110\n";

$server->run();