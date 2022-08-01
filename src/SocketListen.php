<?php

namespace ReactChat;

use React\Socket\ConnectionInterface;
use React\Socket\SocketServer;

class SocketListen
{
    private SocketServer $socket;

    public function __invoke(SocketServer $socket)
    {
        $this->socket = $socket;
        $socket->on('connection', [$this, 'onConnection']);
    }


    public function onConnection(ConnectionInterface $connection)
    {
        (new ConnectionListen)($connection);
    }

}