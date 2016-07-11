<?php

use Krak\HttpMessage,
    GuzzleHttp\Psr7\Request;

describe('HttpMessage', function() {
    describe('Match', function() {
        require __DIR__ . '/match.php';
    });
    describe('#json', function() {
        it('returns the json from the message body', function() {
            $val = [1];
            $req = new Request('GET', '/', [], json_encode($val));
            assert(HttpMessage\json($req) == $val);
        });
    });
    describe('AuthorizationHeader', function() {
        describe('::fromHttpMessage', function() {
            beforeEach(function() {
                $this->req = new Request('GET', '/');
            });
            it('returns null if no header is set', function() {
                $res = HttpMessage\AuthorizationHeader::fromHttpMessage($this->req);
                assert($res === null);
            });
            it('returns null if header is not formatted properly', function() {
                $res = HttpMessage\AuthorizationHeader::fromHttpMessage(
                    $this->req->withHeader('Authorization', '')
                );
                assert($res === null);
            });
        });
        describe('->__toString', function() {
            it('formats the header into a string', function() {
                $header = new HttpMessage\AuthorizationHeader('Test', 'test');
                assert((string) $header == 'Test test');
            });
        });
        describe('->signHttpMessage', function() {
            it('signs the message with the auth header value', function() {
                $header = new HttpMessage\AuthorizationHeader('Test', 'test');
                $req = new Request('GET', '/');
                $req = $header->signHttpMessage($req);
                assert($req->getHeader('Authorization')[0] === 'Test test');
            });
        });
    });
});
