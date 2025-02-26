<?php

// archivo: server.php
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use App\WebSockets\WebSocketServer;

require 'vendor/autoload.php';

// Ruta a los archivos del certificado y la clave privada
$sslCert = './server.crt';
$sslKey = './server.key';

// Crea el servidor WebSocket con SSL
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new WebSocketServer()
        )
    ),
    8080, // Puerto WebSocket
    '0.0.0.0', // Dirección del servidor
    $sslCert,
    $sslKey
);

echo "Servidor WebSocket iniciado en wss://127.0.0.1:8080\n";
$server->run();
