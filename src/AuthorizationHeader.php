<?php

namespace Krak\HttpMessage;

use Psr\Http\Message\MessageInterface;

class AuthorizationHeader
{
    public $scheme;
    public $credentials;

    public function __construct($scheme, $credentials) {
        $this->scheme = $scheme;
        $this->credentials = $credentials;
    }

    public static function fromHttpMessage(MessageInterface $msg, $header_name = 'Authorization') {
        $header = $msg->getHeader($header_name);
        if (!$header) {
            return null;
        }

        $matches = [];
        if (!preg_match('/^\s*(\S+)\s+(\S+)/', $header[0], $matches)) {
            return null;
        }

        return new self($matches[1], $matches[2]);
    }

    public function signHttpMessage(MessageInterface $msg, $header_name = 'Authorization') {
        return $msg->withHeader($header_name, $this);
    }

    public function __toString() {
        return sprintf('%s %s', $this->scheme, $this->credentials);
    }
}
