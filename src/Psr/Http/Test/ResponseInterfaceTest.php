<?php

namespace Psr\Http\Test;

use Psr\Http\ResponseInterface;

/**
 * Provides a base test class for ensuring compliance with the ResponseInterface.
 *
 * Implementers can extend the class and implement abstract methods to run this
 * as part of their test suite.
 */
abstract class ResponseInterfaceTest extends MessageInterfaceTest
{
    /**
     * Gets the ResponseInterface object to test.
     *
     * @return ResponseInterface
     */
    abstract protected function getResponse();

    /**
     * @var ResponseInterface Object to test.
     */
    protected $message;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->message = $this->getResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function testImplements()
    {
        $this->assertInstanceOf('Psr\Http\ResponseInterface', $this->message);
    }

    /**
     * {@inheritdoc}
     */
    public function testToString()
    {
        $this->message
            ->setProtocolVersion('1.1')
            ->setStatusCode(200)
            ->setReasonPhrase('Test')
            ->setHeaders(array('X-Test' => 'Test'))
            ->setBody('<test></test>')
        ;

        $lines = array(
            'HTTP/1.1 200 Test',
            'X-Test: Test',
            '',
            '<test></test>',
        );

        $this->assertEquals(implode("\r\n", $lines), (string) $this->message);
    }

    public function testSetStatusCode()
    {
        $result = $this->message->setStatusCode(200);
        $this->assertEquals(200, $this->message->getStatusCode());
        $this->assertSame($this->message, $result);
    }

    /**
     * @expectedException \Psr\Http\Exception\InvalidArgumentException
     */
    public function testSetStatusCodeInvalidArgumentExceptionOnNonString()
    {
        $this->message->setStatusCode('test');
    }

    /**
     * @expectedException \Psr\Http\Exception\InvalidArgumentException
     */
    public function testSetStatusCodeInvalidArgumentExceptionOnCodeTooLow()
    {
        $this->message->setStatusCode(99);
    }

    /**
     * @expectedException \Psr\Http\Exception\InvalidArgumentException
     */
    public function testSetStatusCodeInvalidArgumentExceptionOnCodeTooHigh()
    {
        $this->message->setStatusCode(600);
    }

    public function testSetReasonPhrase()
    {
        $result = $this->message->setReasonPhrase('Test');
        $this->assertEquals('Test', $this->message->getReasonPhrase());
        $this->assertSame($this->message, $result);
    }

    /**
     * This data provider is not used in this class as using the default reason
     * phrases is optional, but is provided so that the test can be implemented
     * easily.
     *
     * You can implement the test like this:
     *
     * <code>
     * /**
     * * @dataProvider defaultReasonPhraseProvider
     * * /
     * public function testDefaultReasonPhrase($code, $phrase)
     * {
     *     $this->message->setStatusCode($code);
     *     $this->message->setReasonPhrase(null);
     *     $this->assertEquals($phrase, $this->message->getReasonPhrase());
     * }
     * </code>
     *
     * @return array Default reason phrases.
     */
    public function defaultReasonPhraseProvider()
    {
        return array(
            array(100, 'Continue'),
            array(101, 'Switching Protocols'),
            array(102, 'Processing'),
            array(200, 'OK'),
            array(201, 'Created'),
            array(202, 'Accepted'),
            array(203, 'Non-Authoritative Information'),
            array(204, 'No Content'),
            array(205, 'Reset Content'),
            array(206, 'Partial Content'),
            array(207, 'Multi-Status'),
            array(208, 'Already Reported'),
            array(226, 'IM Used'),
            array(300, 'Multiple Choices'),
            array(301, 'Moved Permanently'),
            array(302, 'Found'),
            array(303, 'See Other'),
            array(304, 'Not Modified'),
            array(305, 'Use Proxy'),
            array(306, 'Reserved'),
            array(307, 'Temporary Redirect'),
            array(308, 'Permanent Redirect'),
            array(400, 'Bad Request'),
            array(401, 'Unauthorized'),
            array(402, 'Payment Required'),
            array(403, 'Forbidden'),
            array(404, 'Not Found'),
            array(405, 'Method Not Allowed'),
            array(406, 'Not Acceptable'),
            array(407, 'Proxy Authentication Required'),
            array(408, 'Request Timeout'),
            array(409, 'Conflict'),
            array(410, 'Gone'),
            array(411, 'Length Required'),
            array(412, 'Precondition Failed'),
            array(413, 'Request Entity Too Large'),
            array(414, 'Request-URI Too Long'),
            array(415, 'Unsupported Media Type'),
            array(416, 'Requested Range Not Satisfiable'),
            array(417, 'Expectation Failed'),
            array(418, 'I\'m a teapot'),
            array(422, 'Unprocessable Entity'),
            array(423, 'Locked'),
            array(424, 'Failed Dependency'),
            array(425, 'Reserved for WebDAV advanced collections expired proposal'),
            array(426, 'Upgrade Required'),
            array(428, 'Precondition Required'),
            array(429, 'Too Many Requests'),
            array(431, 'Request Header Fields Too Large'),
            array(500, 'Internal Server Error'),
            array(501, 'Not Implemented'),
            array(502, 'Bad Gateway'),
            array(503, 'Service Unavailable'),
            array(504, 'Gateway Timeout'),
            array(505, 'HTTP Version Not Supported'),
            array(506, 'Variant Also Negotiates (Experimental)'),
            array(507, 'Insufficient Storage'),
            array(508, 'Loop Detected'),
            array(510, 'Not Extended'),
            array(511, 'Network Authentication Required'),
            array(199, null),
        );
    }
}
