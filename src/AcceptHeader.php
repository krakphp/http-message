<?php

namespace Krak\HttpMessage;

use Psr\Http\Message\MessageInterface;

class AcceptHeader
{
    private $items;

    /** $items is an array [AcceptHeaderItem] */
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

        $parts = explode(',', $header);
        $parts = array_map('trim', $parts);

        $parts = array_reduce($parts, function($acc, $part) {
            $values = explode(';', $part);
            $media_range = $values[0];
            $params = array_reduce(array_slice($values, 1), function($acc, $param) {
                list($k, $v) = explode('=', $param);
                $acc[$k] = $v;
                return $acc;
            }, []);

            $acc[] = new AcceptHeaderItem($media_range, $params);
            return $acc;
        }, []);

        // now we need to sort by priority
        usort($parts, function($a, $b) {
            return $a->cmp($b);
        });

        return new self($parts);
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
