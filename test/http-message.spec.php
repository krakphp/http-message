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
    describe('AcceptHeader', function() {
        describe('::fromHttpMessage', function() {
            beforeEach(function() {
                $this->req = new Request('GET', '/');
            });
            it('returns null if no header is set', function() {
                $res = HttpMessage\AcceptHeader::fromHttpMessage($this->req);
                assert($res === null);
            });
            it('returns null if header is not formatted properly', function() {
                $res = HttpMessage\AcceptHeader::fromHttpMessage(
                    $this->req->withHeader('Accept', '')
                );
                assert($res === null);
            });
        });
        describe('::fromString', function() {
            it('builds the header from the header string', function() {
                $header = 'text/*;q=0.8, text/html;q=0.8, text/plain';
                $acc_header = HttpMessage\AcceptHeader::fromString($header);
                assert($acc_header->getAcceptableContentTypes()[1]->getMediaType() == 'text/html');
            });
        });
        describe('->__toString', function() {
            it('formats the header into a string', function() {
                $header = new HttpMessage\AcceptHeader([
                    new HttpMessage\AcceptHeaderItem('text/*', [
                        'q' => 0.8
                    ]),
                    new HttpMessage\AcceptHeaderItem('text/html', [
                        'q' => 0.8
                    ]),
                    new HttpMessage\AcceptHeaderItem('text/plain', []),
                ]);
                assert((string) $header == 'text/*;q=0.8, text/html;q=0.8, text/plain');
            });
        });
        describe('->signHttpMessage', function() {
            it('signs the message with the auth header value', function() {
                $header = new HttpMessage\AcceptHeader([
                    new HttpMessage\AcceptHeaderItem('text/plain', []),
                ]);
                $req = new Request('GET', '/');
                $req = $header->signHttpMessage($req);
                assert($req->getHeader('Accept')[0] === 'text/plain');
            });
        });
    });
    describe('ContentTypeHeader', function() {
        describe('::fromHttpMessage', function() {
            beforeEach(function() {
                $this->req = new Request('GET', '/');
            });
            it('returns null if no header is set', function() {
                $res = HttpMessage\ContentTypeHeader::fromHttpMessage($this->req);
                assert($res === null);
            });
            it('returns null if header is not formatted properly', function() {
                $res = HttpMessage\ContentTypeHeader::fromHttpMessage(
                    $this->req->withHeader('Accept', '')
                );
                assert($res === null);
            });
        });
        describe('::fromString', function() {
            it('builds the header from the header string', function() {
                $header = 'text/html;q=0.8;a=1';
                $acc_header = HttpMessage\ContentTypeHeader::fromString($header);
                assert($acc_header->getContentType()->getMediaType() == 'text/html');
            });
        });
        describe('->__toString', function() {
            it('formats the header into a string', function() {
                $header = new HttpMessage\ContentTypeHeader(
                    new HttpMessage\MediaType\MediaType('text/html', [
                        'q' => 0.8
                    ])
                );
                assert((string) $header == 'text/html;q=0.8');
            });
        });
        describe('->signHttpMessage', function() {
            it('signs the message with the auth header value', function() {
                $header = new HttpMessage\ContentTypeHeader(
                    new HttpMessage\MediaType\MediaType('application/json', ['charset' => 'UTF-8'])
                );
                $req = new Request('GET', '/');
                $req = $header->signHttpMessage($req);
                assert($req->getHeader('Content-Type')[0] === 'application/json;charset=UTF-8');
            });
        });
    });

});
