<?php

namespace Krak\HttpMessage;

use Psr\Http\Message\MessageInterface;

class AcceptHeader
{
    private $items;

    /** $items is an array [MediaType\MediaType] */
    public function __construct(array $items) {
        $this->items = $items;
    }

    public static function fromHttpMessage(MessageInterface $msg, $header_name = 'Accept') {
        $header = $msg->getHeader($header_name);
        if (!$header) {
            return null;
        }

        return self::fromString($header[0]);
    }

    public static function fromString($header) {
        if (!$header) {
            return;
        }

        $parser = new MediaType\MediaTypeParser();
        $types = $parser->parseMediaType($header);
        return new self($types);
    }

    public function signHttpMessage(MessageInterface $msg, $header_name = 'Accept') {
        return $msg->withHeader($header_name, $this);
    }

    /** returns an array of the content types sorted by priority */
    public function getAcceptableContentTypes() {
        return $this->items;
    }

    public function __toString() {
        $parts = array_map(function($item) {
            return (string) $item;
        }, $this->items);

        return implode(', ', $parts);
    }
}
