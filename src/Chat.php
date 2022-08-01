<?php

namespace ReactChat;

use Colors\Color;

class Chat
{
    private static ?self $instance = null;
    private \SplObjectStorage $clients;
    private Color $c;
    
    private function __construct()
    {
        $this->clients = new \SplObjectStorage();
        $this->c = new Color();
    }

    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public function addClient(Client $client): bool
    {
        $c = $this->c;

        if (!$this->clients->contains($client)) {
            $this->clients->attach($client);
            $client->send("Welcome {$client->name}!" . PHP_EOL);
            $this->messageAll(
                $c("<yellow>{$client->name}</yellow> <green>has joined the chat</green>" . PHP_EOL)->colorize(), 
                $client
            );
            return true;
        }

        return false;
    }

    public function removeClient(Client $client): bool
    {
        if ($this->clients->contains($client)) {
            $this->clients->detach($client);
            return true;
        }

        return false;
    }

    public function messageTo(string $message, string $name): void
    {
        $message = preg_replace('/\B(@[^\s+]+)/', '<green>$1</green>', $message);

        $c = $this->c;

        foreach($this->clients as $client) {
            if($client->name === $name) {
                $client->send($c($message)->colorize());
            }
        }
    }

    public function messageAll(string $message, Client $exceptClient): void
    {
        foreach ($this->clients as $client) {
            if($client !== $exceptClient) {
                $client->send($message);
            }
        }
    }
}