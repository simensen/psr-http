<?php

namespace Psr\Http;

use Psr\Http\AbstractResponse;
use Psr\Http\Test\ResponseInterfaceTest;

class AbstractResponseTest extends ResponseInterfaceTest
{
    public function getResponse()
    {
        return $this->getMockForAbstractClass('Psr\Http\AbstractResponse', array(200));
    }

    /**
     * @dataProvider defaultReasonPhraseProvider
     */
    public function testDefaultReasonPhrase($code, $phrase)
    {
        $this->message->setStatusCode($code);
        $this->message->setReasonPhrase(null);
        $this->assertEquals($phrase, $this->message->getReasonPhrase());
    }
}
