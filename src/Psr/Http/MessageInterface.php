<?php

namespace Psr\Http;

use Psr\Http\Exception\InvalidArgumentException;

/**
 * HTTP messages consist of requests from client to server and responses from
 * server to client.
 *
 * This interface is not to be implemented directly, instead implement
 * `RequestInterface` or `ResponseInterface` as appropriate.
 */
interface MessageInterface
{
    /**
     * Returns the message as an HTTP string.
     *
     * @return string Message as an HTTP string.
     */
    public function __toString();

    /**
     * Gets the HTTP protocol version.
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion();

    /**
     * Sets the HTTP protocol version.
     *
     * @param string $protocolVersion The HTTP protocol version.
     *
     * @return self Reference to the message.
     *
     * @throws InvalidArgumentException When the HTTP protocol version is not valid.
     */
    public function setProtocolVersion($protocolVersion);

    /**
     * Gets a header.
     *
     * @param string $header Header name.
     *
     * @return string|null Header value, or null if not set.
     */
    public function getHeader($header);

    /**
     * Gets all headers.
     *
     * The array keys are the header name, the values the header value.
     *
     * @return array Headers.
     */
    public function getHeaders();

    /**
     * Checks if a certain header is present.
     *
     * @param string $header Header name.
     *
     * @return bool If the header is present.
     */
    public function hasHeader($header);

    /**
     * Sets a header, replacing the existing header if has already been set.
     *
     * The header name and value MUST be a string, or an object that implement
     * the `__toString()` method. The value MAY also be an array, in which case
     * it MUST be converted to a comma-separated string; the ordering MUST be
     * maintained.
     *
     * A null value will remove the existing header.
     *
     * @param string $header Header name.
     * @param string $value  Header value.
     *
     * @return self Reference to the message.
     *
     * @throws InvalidArgumentException When the header name or value is not valid.
     */
    public function setHeader($header, $value);

    /**
     * Sets headers, removing any that have already been set.
     *
     * The array keys must the header name, the values the header value.
     *
     * The header names and values MUST strings, or objects that implement the
     * `__toString()` method. The values MAY also be arrays, in which case they
     * MUST be converted to comma-separated strings; the ordering MUST be
     * maintained.
     *
     * @param array $headers Headers to set.
     *
     * @return self Reference to the message.
     *
     * @throws InvalidArgumentException When part of the header set is not valid.
     */
    public function setHeaders(array $headers);

    /**
     * Adds headers, replacing those that are already set.
     *
     * The array keys must the header name, the values the header value.
     *
     * The header names and values MUST strings, or objects that implement the
     * `__toString()` method. The values MAY also be arrays, in which case they
     * MUST be converted to comma-separated strings; the ordering MUST be
     * maintained.
     *
     * Null values will remove existing headers.
     *
     * @param array $headers Headers to add.
     *
     * @return self Reference to the message.
     *
     * @throws InvalidArgumentException When part of the header set is not valid.
     */
    public function addHeaders(array $headers);

    /**
     * Gets the body.
     *
     * This returns the original form, in contrast to `getBodyAsString()`.
     *
     * @return mixed|null Body, or null if not set.
     *
     * @see getBodyAsString()
     */
    public function getBody();

    /**
     * Gets the body as a string.
     *
     * If the body is a stream, it will be read at this point.
     *
     * @return string|null Body as a string, or null if not set.
     */
    public function getBodyAsString();

    /**
     * Sets the body.
     *
     * The body SHOULD be one of:
     *
     * - string
     * - object that implements `Serializable`
     * - `SimpleXMLElement`
     * - stream
     * - null
     * - callable that returns one of the above
     *
     * An implementation MUST reject any other form that it does not know how
     * to turn into a string.
     *
     * A callable MUST be called at this point to verify the return type.
     *
     * Anything other than a stream MAY immediately be turned into a string;
     * both forms MUST be stored.
     *
     * An implementation MUST NOT read a stream at this point.
     *
     * A null value will remove the existing body.
     *
     * @param mixed $body Body.
     *
     * @return self Reference to the message.
     *
     * @throws InvalidArgumentException When the body is not valid.
     */
    public function setBody($body);
}
