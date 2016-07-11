<?php

namespace Krak\HttpMessage\Match;

use Psr\Http\Message\RequestInterface;

const CMP_RE = 1; // regular expression
const CMP_IN = 2; // contains
const CMP_EQ = 3; // equals
const CMP_SW = 4; // starts with

function _cmp($pattern, $path, $cmp) {
    switch ($cmp) {
    case CMP_RE: return preg_match($pattern, $path);
    case CMP_IN: return strpos($path, $pattern) !== false;
    case CMP_EQ: return strcmp($pattern, $path) === 0;
    case CMP_SW: return strpos($path, $pattern) === 0;
    }
}

function path($pattern, $cmp = CMP_RE) {
    return function(RequestInterface $req) use ($pattern, $cmp) {
        $path = $req->getUri()->getPath();
        return _cmp($pattern, $path, $cmp);
    };
}

function method($methods) {
    return function(RequestInterface $req) use ($methods) {
        return in_array($req->getMethod(), $methods);
    };
}

function routes($routes) {
    return function(RequestInterface $req) use ($routes) {
        foreach ($routes as $route) {
            list($methods, $path) = $route;
            $cmp = count($route) === 3 ? $route[2] : CMP_RE;
            $method_match =
                (is_array($methods) && in_array($req->getMethod(), $methods))
                ||
                (is_string($methods) && strcasecmp($methods, $req->getMethod()) == 0);
            if (!$method_match) {
                continue;
            }
            if (_cmp($path, $req->getUri()->getPath(), $cmp)) {
                return true;
            }
        }

        return false;
    };
}


/** check on the header, if no pattern is provided, this checks if the header
    exists. If the value is provided, it gets the header value and does a
    comparison */
function header($header_name, $pattern = null, $cmp = CMP_RE) {
    return function(RequestInterface $req) use ($header_name, $pattern, $cmp) {
        $header_values = $req->getHeader($header_name);

        if (!$pattern) {
            return count($header_values) > 0;
        }

        foreach ($header_values as $value) {
            if (_cmp($pattern, $value, $cmp)) {
                return true;
            }
        }

        return false;
    };
}

function orx(array $filters) {
    return function(RequestInterface $req) use ($filters) {
        return array_reduce($filters, function($acc, $filter) use ($req) {
            return $acc || $filter($req);
        }, false);
    };
}

function andx(array $filters) {
    return function(RequestInterface $req) use ($filters) {
        return array_reduce($filters, function($acc, $filter) use ($req) {
            return $acc && $filter($req);
        }, true);
    };
}

function not($filter) {
    return function(RequestInterface $req) use ($filter) {
        return !$filter($req);
    };
}

function always($res) {
    return function(RequestInterface $req) use ($res) {
        return $res;
    };
}
