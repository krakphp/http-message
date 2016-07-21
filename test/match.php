<?php

use Krak\HttpMessage\Match,
    GuzzleHttp\Psr7\Request;

beforeEach(function() {
    $this->req = new Request('GET', '/api');
});

describe('#_cmp', function() {
    it('does a preg match on CMP_RE', function() {
        assert(Match\_cmp('~ab*c~', 'ac', Match\CMP_RE));
    });
    it('checks if str contains in string on CMP_IN', function() {
        assert(Match\_cmp('db', 'adbc', Match\CMP_IN));
    });
    it('checks if str == str on CMP_EQ', function() {
        assert(Match\_cmp('abc', 'abc', Match\CMP_EQ));
    });
    it('checks if str starts with str on CMP_SW', function() {
        assert(Match\_cmp('abc', 'abcdef', Match\CMP_SW));
    });
});
describe('#path', function() {
    it('matches a requests path', function() {
        $match = match\path('/api', match\CMP_EQ);
        assert($match($this->req));
    });
});
describe('#method', function() {
    it('matches a requests method', function() {
        $match = match\method(['GET']);
        assert($match($this->req));
    });
});
describe('#routes', function() {
    it('allows methods as string', function() {
        $match = match\routes([
            ['GET', '~/api~']
        ]);
        assert($match($this->req));
    });
    it('allows methods as an array', function() {
        $match = match\routes([
            [['POST', 'GET'], '~/api~']
        ]);
        assert($match($this->req));
    });
    it('allows an optional CMP parameter per route', function() {
        $match = match\routes([
            ['GET', '/api', match\CMP_EQ]
        ]);
        assert($match($this->req));
    });
});
describe('#route', function() {
    it('matches a single route', function() {
        $match = match\route('GET', '/api');
        assert($match($this->req));
    });
});
describe('#header', function() {
    beforeEach(function() {
        $this->req = $this->req->withHeader('Content-Type', 'application/json');
    });
    it('checks if a headers exists if no $pattern is given', function() {
        $match = match\header('Content-Type');
        assert($match($this->req));
    });
    it('checks a headers value if a $patter is given', function() {
        $match = match\header('Content-Type', 'application/json', match\CMP_EQ);
        assert($match($this->req));
    });
});
describe('#always', function() {
    it('returns the same values... always', function() {
        $match = match\always(true);
        assert($match($this->req));
    });
});
describe('#orx', function() {
    it('OR\'s multiple filters together', function() {
        $match = match\orx([match\always(false), match\always(true)]);
        assert($match($this->req));
    });
});
describe('#andx', function() {
    it('AND\'s multiple filters together', function() {
        $match = match\andx([match\always(true), match\always(false)]);
        assert(!$match($this->req));
    });
});
describe('#not', function() {
    it('!\'s a filter result', function() {
        $match = match\not(match\always(false));
        assert($match($this->req));
    });
});
