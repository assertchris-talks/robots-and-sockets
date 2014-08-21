<?php

require("vendor/autoload.php");
require("grid.php");

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

$server = IoServer::factory(
  new HttpServer(
    new WsServer(
      new Grid()
    )
  ),
  8002,
  "192.168.0.31"
);

echo "Socket server on port 8002";

$server->run();
