<?php

namespace Psr\Http;

use Psr\Http\Exception\InvalidArgumentException;

/**
 * `RequestInterface` implementation that other messages can inherit from.
 */
abstract class AbstractRequest extends AbstractMessage implements RequestInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct($method, $url, $protocolVersion = '1.1')
    {
        $this->setMethod($method);
        $this->setUrl($url);
        $this->setProtocolVersion($protocolVersion);
    }

    /**
     * {@inheritdoc}
     */
    protected function getStartLine()
    {
        return sprintf('%s %s HTTP/%s', $this->getMethod(), $this->getPath(), $this->getProtocolVersion());
    }

    /**
     * @var string Method.
     */
    protected $method;

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @var array URL parts.
     */
    protected $url;

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return http_build_url('', $this->url);
    }

    protected function getPath()
    {
        return implode('', array($this->url['path'], $this->url['query'], $this->url['fragment']));
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($url)
    {
        $url = parse_url($url);

        if (false === $url || false === array_key_exists('scheme', $url)) {
            throw new InvalidArgumentException('Invalid absolute URL given');
        }

        $this->url = array_merge(
            array(
                'scheme' => null,
                'host' => null,
                'port' => null,
                'user' => null,
                'pass' => null,
                'path' => null,
                'query' => null,
                'fragment' => null,
            ),
            $url
        );

        if (
            ('http' === $this->url['scheme'] && null !== $this->url['port'] && 80 !== $this->url['port']) ||
            ('https' === $this->url['scheme'] && null !== $this->url['port'] && 443 !== $this->url['port'])
        ) {
            $this->setHeader('Host', sprintf('%s:%s', $this->url['host'], $this->url['port']));
        } else {
            $this->setHeader('Host', $this->url['host']);
        }

        return $this;
    }
}
