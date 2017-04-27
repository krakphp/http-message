<?php

namespace Krak\HttpMessage;

use Psr\Http\Message\MessageInterface;

class ContentTypeHeader
{
    private $type;

    /** $items is an array [MediaType\MediaType] */
    public function __construct(MediaType\MediaType $type) {
        $this->type = $type;
    }

    public static function fromHttpMessage(MessageInterface $msg, $header_name = 'Content-Type') {
        $header = $msg->getHeader($header_name);
        if (!$header) {
            return;
        }

        return self::fromString($header[0]);
    }

    public static function fromString($header) {
        if (!$header) {
            return;
        }

        $parser = new MediaType\MediaTypeParser();
        $types = $parser->parseMediaType($header);
        return new self($types[0]);
    }

    public function signHttpMessage(MessageInterface $msg, $header_name = 'Content-Type') {
        return $msg->withHeader($header_name, $this);
    }

    public function getContentType() {
        return $this->type;
    }

    public function __toString() {
        return (string) $this->type;
    }
}
