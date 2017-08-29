<?php

namespace AppBundle\Service;

class MessageGenerator
{
    private $messages;

    public function __construct(array $messages)
    {
        $this->messages = $messages;
    }

    public function getMessage()
    {
        return $this->messages[array_rand($this->messages)];
    }
}
