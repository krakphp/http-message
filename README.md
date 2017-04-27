# Http Message Utilities

Simple utilities for the psr http message (psr7) specification.

## Headers

We provide several utilities for parsing special headers.

Each header class is named after the specific http header and has the following functions for parsing and signing.

Here's an example using the AcceptHeader class

```php
<?php

use Krak\HttpMessage;

// grab from a psr http message
$accept_header = HttpMessage\AcceptHeader::fromHttpMessage($psr_req);
// parse from a string
$accept_header = HttpMessage\AcceptHeader::fromString('text/*;q=0.1,text/html;q=0.2');

// You can then sign an http message with the header, which will write the value of the header into the req.
$req = $accept_header->signHttpMessage($req);

// You can also export the header as a string
echo $accept_header;
```

We provide abstractions with the following classes:

- `Krak\HttpMessage\AuthorizationHeader`
- `Krak\HttpMessage\AcceptHeader`
- `Krak\HttpMessage\ContentTypeHeader`

## Matchers

The `Krak\HttpMessage\Match` directory holds functional utilities for creating matchers for http message requests.
