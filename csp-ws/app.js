'use strict';

const express      = require('express');
const SocketServer = require('ws').Server;
const path         = require('path');
const querystring = require('querystring');


const PORT = process.env.PORT || 3000;
const INDEX = path.join(__dirname, 'index.html');

const reports = {};

const server = express()
  .use((req, res) => res.sendFile(INDEX) )
  .listen(PORT, () => console.log(`Node is ... Listening on ${ PORT }`));

const wss = new SocketServer({ server });

wss.on('connection', (ws, req) => {
    //  req.headers,
    let tmp = req.url.split("?");
    ws.quid = null;
    if (tmp[1]) {
        let data = querystring.decode(tmp[1]);
        ws.quid = data['id'];

        setTimeout(function() {
            // @todo array pop
            (reports[ws.quid] || []).map(d => {
                ws.send(d);
            });    
        }, 4000);
    }


  console.log('Client connected', );
  ws.on('close', () => console.log('Client disconnected'));
  ws.on('message', function incoming(data) {
    let doc = JSON.parse(data);
    wss.clients.forEach((client) => {
        console.log(client.quid)
        // client.send(new Date().toTimeString());
    });

    if (!reports[doc.doc_id]) {
        reports[doc.doc_id] = [];
    }
    reports[doc.doc_id].push(data);

    console.log(reports);
    /*
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
        */
  });
});
/*
setInterval(() => {
  wss.clients.forEach((client) => {
    client.send(new Date().toTimeString());
  });
}, 1000);
*/



  