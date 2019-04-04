<?php
namespace CSP;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Report implements MessageComponentInterface {
    protected $clients;

    public function __construct($logPath) 
    {
        $this->logPath = $logPath;
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        $query = $conn->httpRequest->getUri()->getQuery();
        parse_str($query, $data);
        $doc_id = $data['id'];
        $conn->send("Hi?");
        sleep(3);

        if (preg_match('/^[a-z0-9]+$/', $doc_id)) {
            if ($file = fopen("{$this->logPath}/doc-$doc_id.log", "r")) {
                while(!feof($file)) {
                    $line = fgets($file);
                    $conn->send($line);
                }
                fclose($file);
            }
        } else {
            $msg = json_encode($data);
            $conn->send($msg);
        }

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}