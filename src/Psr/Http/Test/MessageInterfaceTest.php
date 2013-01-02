<?php

namespace Psr\Http\Test;

use PHPUnit_Framework_TestCase;
use Psr\Http\MessageInterface;
use Psr\Http\Test\Fixtures\SerializableObject;
use SimpleXMLElement;

/**
 * Provides a base test class for ensuring compliance with the MessageInterface.
 *
 * This test class is not to be implemented directly, instead implement
 * `RequestInterfaceTest` or `ResponseInterfaceTest` as appropriate.
 */
abstract class MessageInterfaceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var MessageInterface Object to test.
     */
    protected $message;

    abstract public function testImplements();

    abstract public function testToString();

    public function testSetProtocolVersion()
    {
        $result = $this->message->setProtocolVersion('1.1');
        $this->assertEquals('1.1', $this->message->getProtocolVersion());
        $this->assertSame($this->message, $result);
    }

    /**
     * @expectedException \Psr\Http\Exception\InvalidArgumentException
     */
    public function testSetProtocolVersionInvalidArgumentExceptionOnNonString()
    {
        $this->message->setProtocolVersion(1);
    }

    /**
     * @expectedException \Psr\Http\Exception\InvalidArgumentException
     */
    public function testSetProtocolVersionInvalidArgumentExceptionOnUnknownVersion()
    {
        $this->message->setProtocolVersion('10.0');
    }

    public function testHeaders()
    {
        $this->message->setHeaders(array());
        $this->assertEquals(array(), $this->message->getHeaders());
        $this->assertFalse($this->message->hasHeader('Test'));

        $this->message->setHeader('Test', 'Test');
        $this->assertTrue($this->message->hasHeader('Test'));
        $this->assertEquals('Test', $this->message->getHeader('Test'));

        $this->message->setHeaders(array('Test1' => 'Test One', 'Test2' => 'Test Two'));
        $this->assertEquals('Test One', $this->message->getHeader('Test1'));
        $this->assertEquals('Test Two', $this->message->getHeader('Test2'));
        $headers = $this->message->getHeaders();
        $this->assertCount(2, $headers);
        $this->assertArrayHasKey('Test1', $headers);
        $this->assertEquals('Test One', $headers['Test1']);
        $this->assertArrayHasKey('Test2', $headers);
        $this->assertEquals('Test Two', $headers['Test2']);

        $this->message->addHeaders(array('Test3' => 'Test Three'));
        $this->assertEquals('Test One', $this->message->getHeader('Test1'));
        $this->assertEquals('Test Two', $this->message->getHeader('Test2'));
        $this->assertEquals('Test Three', $this->message->getHeader('Test3'));
        $headers = $this->message->getHeaders();
        $this->assertCount(3, $headers);
        $this->assertArrayHasKey('Test1', $headers);
        $this->assertEquals('Test One', $headers['Test1']);
        $this->assertArrayHasKey('Test2', $headers);
        $this->assertEquals('Test Two', $headers['Test2']);
        $this->assertArrayHasKey('Test3', $headers);
        $this->assertEquals('Test Three', $headers['Test3']);

        $this->message->setHeader('Test2', null);
        $this->assertEquals('Test One', $this->message->getHeader('Test1'));
        $this->assertNull($this->message->getHeader('Test2'));
        $this->assertEquals('Test Three', $this->message->getHeader('Test3'));
        $headers = $this->message->getHeaders();
        $this->assertCount(2, $headers);
        $this->assertArrayHasKey('Test1', $headers);
        $this->assertEquals('Test One', $headers['Test1']);
        $this->assertArrayNotHasKey('Test2', $headers);
        $this->assertArrayHasKey('Test3', $headers);
        $this->assertEquals('Test Three', $headers['Test3']);

        $this->message->addHeaders(array('Test3' => null));
        $this->assertEquals('Test One', $this->message->getHeader('Test1'));
        $this->assertNull($this->message->getHeader('Test2'));
        $this->assertNull($this->message->getHeader('Test3'));
        $headers = $this->message->getHeaders();
        $this->assertCount(1, $headers);
        $this->assertArrayHasKey('Test1', $headers);
        $this->assertEquals('Test One', $headers['Test1']);
        $this->assertArrayNotHasKey('Test2', $headers);
        $this->assertArrayNotHasKey('Test3', $headers);
    }

    public function testBodyWithString()
    {
        $this->message->setBody('test');
        $this->assertSame('test', $this->message->getBody());
        $this->assertEquals('test', $this->message->getBodyAsString());
    }

    public function testBodyWithCallableString()
    {
        $callable = function () {
            return 'test';
        };

        $this->message->setBody($callable);
        $this->assertSame('test', $this->message->getBody());
        $this->assertEquals('test', $this->message->getBodyAsString());
    }

    public function testBodyWithSerializableObject()
    {
        $object = new SerializableObject();
        $object->data = array('foo' => 'bar');

        $this->message->setBody($object);
        $this->assertSame($object, $this->message->getBody());
        $this->assertEquals($object->serialize(), $this->message->getBodyAsString());
    }

    public function testBodyWithCallableSerializableObject()
    {
        $object = new SerializableObject();
        $object->data = array('foo' => 'bar');

        $callable = function () use ($object) {
            return $object;
        };

        $this->message->setBody($callable);
        $this->assertSame($object, $this->message->getBody());
        $this->assertEquals($object->serialize(), $this->message->getBodyAsString());
    }

    public function testBodyWithSimpleXMLElement()
    {
        $xml = new SimpleXMLElement('<foo>bar</foo>');

        $this->message->setBody($xml);
        $this->assertSame($xml, $this->message->getBody());
        $this->assertEquals($xml->asXML(), $this->message->getBodyAsString());
    }

    public function testBodyWithCallableSimpleXMLElement()
    {
        $xml = new SimpleXMLElement('<foo>bar</foo>');

        $callable = function () use ($xml) {
            return $xml;
        };

        $this->message->setBody($callable);
        $this->assertSame($xml, $this->message->getBody());
        $this->assertEquals($xml->asXML(), $this->message->getBodyAsString());
    }

    public function testBodyWithStream()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'text.txt';
        if (false === $stream = @fopen($path, 'r')) {
            $this->markTestSkipped('Unable to load file');
        }

        $this->message->setBody($stream);
        $this->assertSame($stream, $this->message->getBody());
        $this->assertEquals(file_get_contents($path), $this->message->getBodyAsString());
    }

    public function testBodyWithCallableStream()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'text.txt';
        if (false === $stream = @fopen($path, 'r')) {
            $this->markTestSkipped('Unable to load file');
        }

        $callable = function () use ($stream) {
            return $stream;
        };

        $this->message->setBody($callable);
        $this->assertSame($stream, $this->message->getBody());
        $this->assertEquals(file_get_contents($path), $this->message->getBodyAsString());
    }

    public function testBodyWithNull()
    {
        $this->message->setBody(null);
        $this->assertNull($this->message->getBody());
        $this->assertNull($this->message->getBodyAsString());
    }

    public function testBodyWithCallableNull()
    {
        $callable = function () {
            return null;
        };

        $this->message->setBody($callable);
        $this->assertNull($this->message->getBody());
        $this->assertNull($this->message->getBodyAsString());
    }

    /**
     * @expectedException \Psr\Http\Exception\InvalidArgumentException
     */
    public function testBodyWithObject()
    {
        $this->message->setBody(new \DateTime());
    }
}
