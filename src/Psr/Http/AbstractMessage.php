<?php

namespace Psr\Http;

use Serializable,
    SimpleXMLElement;
use Psr\Http\Exception\InvalidArgumentException;

/**
 * `MessageInterface` implementation that other messages can inherit from.
 *
 * If extending this class directly, you will need to implement either
 * `RequestInterface` or `ResponseInterface`. Alternatively, you can inherit
 * from `AbstractRequest` or `AbstractResponse`.
 */
abstract class AbstractMessage implements MessageInterface
{
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $parts = array();

        $parts[] = $this->getStartLine();

        foreach ($this->getHeaders() as $header => $value) {
            $parts[] = $header . ': ' . $value;
        }

        $parts[] = null;

        if (null !== $this->getBody()) {
            $parts[] = $this->getBodyAsString();
        }

        return implode("\r\n", $parts);
    }

    /**
     * Gets the start line.
     *
     * @return string Start line.
     */
    abstract protected function getStartLine();

    /**
     * @var string HTTP protocol version.
     */
    protected $protocolVersion;

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function setProtocolVersion($protocolVersion)
    {
        if (false === is_string($protocolVersion)) {
            throw new InvalidArgumentException('The HTTP protocol version is not a string');
        } elseif (false === in_array($protocolVersion, array('1.0', '1.1'), true)) {
            throw new InvalidArgumentException(sprintf('Unknown HTTP protocol version "%s"', $protocolVersion));
        }

        $this->protocolVersion = $protocolVersion;

        return $this;
    }

    /**
     * @var array Headers.
     */
    protected $headers = array();

    /**
     * {@inheritdoc}
     */
    public function getHeader($header)
    {
        return isset($this->headers[$header]) ? $this->headers[$header] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($header)
    {
        return isset($this->headers[$header]);
    }

    /**
     * {@inheritdoc}
     */
    public function setHeader($header, $value)
    {
        if (null === $value) {
            unset($this->headers[$header]);
        } else {
            $this->headers[$header] = $value;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array();

        return $this->addHeaders($headers);
    }

    /**
     * {@inheritdoc}
     */
    public function addHeaders(array $headers)
    {
        foreach ($headers as $header => $value) {
            $this->setHeader($header, $value);
        }

        return $this;
    }

    /**
     * @var mixed Body.
     */
    protected $body;

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function getBodyAsString()
    {
        $body = $this->body;

        if (is_resource($body)) {
            $body = stream_get_contents($body);
        } elseif ($body instanceof Serializable) {
            $body = $body->serialize();
        } elseif ($body instanceof SimpleXMLElement) {
            $body = $body->asXML();
        }

        if (null === $body) {
            return null;
        }

        return (string) $body;
    }

    /**
     * {@inheritdoc}
     */
    public function setBody($body)
    {
        $clean = null;

        if (is_callable($body)) {
            $body = call_user_func($body);
        }

        if (is_string($body) ||
            (is_resource($body) && 'stream' === get_resource_type($body)) ||
            (is_object($body) && $body instanceof Serializable) ||
            ($body instanceof SimpleXMLElement)
        ) {
            $clean = $body;
        }

        if (null === $clean && null !== $body) {
            throw new InvalidArgumentException(sprintf('The body is not valid, %s given', gettype($body)));
        }

        $this->body = $clean;

        return $this;
    }
}
