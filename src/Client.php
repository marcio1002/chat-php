<?php

namespace ReactChat;

use React\Socket\ConnectionInterface;

class Client
{
    private string $name;
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function __set($property, $value)
    {
        $this->hasProperty($property);

        $this->$property = $value;
    }

    public function __get($property)
    {
        $this->hasProperty($property);

        return $this->$property ?? null;
    }

    private function hasProperty($name)
    {
        if (!property_exists($this, $name)) {
            throw new \Exception("Property $name does not exist");
        }
    }


    public function send(string $message)
    {
        $this->connection->write($message);
    }
}