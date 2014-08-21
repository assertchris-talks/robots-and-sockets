<?php

require ("vendor/autoload.php");

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);
$http = new React\Http\Server($socket, $loop);

$http->on("request", function($request, $response) {
  $response->writeHead(200, ["Content-Type" => "text/html"]);
  $response->end(file_get_contents("index.html"));
});

echo "HTTP server on port 8001";

$socket->listen(8001);
$loop->run();
