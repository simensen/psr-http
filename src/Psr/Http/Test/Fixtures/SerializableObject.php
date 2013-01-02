<?php

namespace Psr\Http\Test\Fixtures;

use Serializable;

class SerializableObject implements Serializable
{
    public $data;

    public function serialize()
    {
        return json_encode($this->data);
    }

    public function unserialize($data)
    {
        $this->data = json_decode($data);
    }
}
