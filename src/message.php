<?php

namespace Krak\HttpMessage;

use Psr\Http\Message\MessageInterface;

/** grabs json from the request body and parses as JSON, this does not do any
    validation in order to parse the content type header. */
function json(MessageInterface $req, $as_array = true) {
    return json_decode($req->getBody(), $as_array);
}
