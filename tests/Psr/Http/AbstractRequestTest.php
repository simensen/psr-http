<?php

namespace Psr\Http;

use Psr\Http\Test\RequestInterfaceTest;

class AbstractRequestTest extends RequestInterfaceTest
{
    public function getRequest()
    {
        return $this->getMockForAbstractClass('Psr\Http\AbstractRequest', array('GET', 'http://www.example.com/'));
    }
}
