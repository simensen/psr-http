<?php

namespace Psr\Http;

use Psr\Http\Exception\InvalidArgumentException;

/**
 * `ResponseInterface` implementation that other messages can inherit from.
 */
abstract class AbstractResponse extends AbstractMessage implements ResponseInterface
{
    /**
     * @var array Default reason phrases.
     */
    public static $defaultReasonPhrases = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Reserved for WebDAV advanced collections expired proposal',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates (Experimental)',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    );

    /**
     * {@inheritdoc}
     */
    public function __construct($statusCode, $reasonPhrase = null, $protocolVersion = '1.1')
    {
        $this->setStatusCode($statusCode);
        $this->setReasonPhrase($reasonPhrase);
        $this->setProtocolVersion($protocolVersion);
    }

    /**
     * {@inheritdoc}
     */
    protected function getStartLine()
    {
        return sprintf('HTTP/%s %s %s', $this->getProtocolVersion(), $this->getStatusCode(), $this->getReasonPhrase());
    }

    /**
     * @var int HTTP status code.
     */
    protected $statusCode;

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatusCode($statusCode)
    {
        if (false === is_int($statusCode)) {
            throw new InvalidArgumentException('The HTTP status code is not an integer');
        } elseif ($statusCode < 100 || $statusCode >= 600) {
            throw new InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $statusCode));
        }

        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @var string|null Reason phrase.
     */
    protected $reasonPhrase;

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {
        if (null !== $this->reasonPhrase) {
            return $this->reasonPhrase;
        } elseif (null !== $this->statusCode && isset(self::$defaultReasonPhrases[$this->statusCode])) {
            return self::$defaultReasonPhrases[$this->statusCode];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setReasonPhrase($reasonPhrase)
    {
        $this->reasonPhrase = $reasonPhrase;

        return $this;
    }
}
