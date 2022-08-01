<?php

namespace ReactChat;

use React\Socket\ConnectionInterface;

class ConnectionListen
{
    private Chat $chat;
    private Client $client;
    private ConnectionInterface $connection;

    public function __construct()
    {
        $this->chat = Chat::getInstance();
    }

    public function __invoke(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        $this->client = new Client($connection);

        $connection->write('Whats your name? ');

        $connection->on('data', [$this, 'onData']);
        $connection->on('close', [$this, 'onClose']);
        $connection->on('error', [$this, 'onError']);
    }


    public function onData($data)
    {
        if(empty(trim($data))) return;

        $data = preg_replace('/^(>)/', '', $data);

        if (trim($data) === 'exit') {
            $this->connection->close();
            $this->chat->removeClient($this->client);
            return;
        }

        if ($this->client && !$this->client->name) {
            $this->client->name = $data = str_replace(["\n", "\r"], "", $data);
            $this->chat->addClient($this->client);
            return;
        }

        if (($mentioned = $this->getMentionedClient($data))) {
            $this->chat->messageTo("{$this->client->name}> $data" . PHP_EOL, $mentioned);
        } else {
            $this->chat->messageAll("{$this->client->name}> {$data}" . PHP_EOL, $this->client);
        }
    }

    public function onClose()
    {
        $this->chat->removeClient($this->client);
        $this->chat->messageAll("{$this->client->name} has left" . PHP_EOL, $this->client);
    }

    public function onError(\Exception $ex)
    {
        $this->connection->write('Error ocurred ' . $ex->getMessage());
        $this->connection->close();
    }

    private function getMentionedClient(string $message)
    {
        preg_match('/\B@(\w+)/i', $message, $matches);

        if (!$matches) {
            return null;
        }

        return $matches[1] ?? null;
    }
}