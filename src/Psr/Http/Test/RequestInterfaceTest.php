<?php

namespace Psr\Http\Test;

use Psr\Http\RequestInterface;

/**
 * Provides a base test class for ensuring compliance with the RequestInterface.
 *
 * Implementers can extend the class and implement abstract methods to run this
 * as part of their test suite.
 */
abstract class RequestInterfaceTest extends MessageInterfaceTest
{
    /**
     * Gets the RequestInterface object to test.
     *
     * @return RequestInterface
     */
    abstract protected function getRequest();

    /**
     * @var RequestInterface Object to test.
     */
    protected $message;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->message = $this->getRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function testImplements()
    {
        $this->assertInstanceOf('Psr\Http\RequestInterface', $this->message);
    }

    /**
     * {@inheritdoc}
     */
    public function testToString()
    {
        $this->message
            ->setProtocolVersion('1.1')
            ->setMethod('POST')
            ->setUrl('http://www.example.com/example.html')
            ->addHeaders(array('X-Test' => 'Test'))
            ->setBody('<test></test>')
        ;

        $lines = array(
            'POST /example.html HTTP/1.1',
            'Host: www.example.com',
            'X-Test: Test',
            '',
            '<test></test>',
        );

        $this->assertEquals(implode("\r\n", $lines), (string) $this->message);
    }

    public function testSetMethod()
    {
        $result = $this->message->setMethod('POST');
        $this->assertEquals('POST', $this->message->getMethod());
        $this->assertSame($this->message, $result);
    }

    /**
     * @dataProvider urlProvider
     */
    public function testSetUrl($url, $success)
    {
        if (false === $success) {
            $this->setExpectedException('Psr\Http\Exception\InvalidArgumentException');
        }

        $result = $this->message->setUrl($url);
        $this->assertEquals($url, $this->message->getUrl());
        $this->assertSame($this->message, $result);
    }

    public function urlProvider()
    {
        return array(
            array('http://www.example.com/', true),
            array('http:///www.example.com/', false),
            array('http://username:password@www.example.com:8080/path?query#fragment', true),
            array('www.example.com', false),
            array('/example', false),
            array('example', false)
        );
    }
}
