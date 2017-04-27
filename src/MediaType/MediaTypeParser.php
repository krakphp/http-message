<?php

namespace Krak\HttpMessage\MediaType;

class MediaTypeParser
{
    /** returns an array of media types from a string and sorted by priority.
        e.g.: application/json;q=0.1;a=1,text/*;q=.8
     */
    public function parseMediaType($header) {
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

            $acc[] = new MediaType($media_range, $params);
            return $acc;
        }, []);

        // now we need to sort by priority
        usort($parts, function($a, $b) {
            return $a->cmp($b);
        });

        return $parts;
    }
}
