<?php

require_once __DIR__ . "/vendor/autoload.php";

use React\Socket\SocketServer;
use React\Stream\WritableResourceStream;
use ReactChat\SocketListen;
use ReactChat\Chat;

$host = $_ENV['host'] ?? '127.0.0.1';
$port = $_ENV['port'] ?? 2222;

$stdout = new WritableResourceStream(STDOUT);
$socket = new SocketServer("$host:$port");

(new SocketListen)($socket);