<?php

namespace Krak\HttpMessage;

class AcceptHeaderItem
{
    private $media_type;
    private $params;

    public function __construct($media_type, array $params) {
        $this->media_type = $media_type;
        $this->params = $params;
    }

    public function getMediaType() {
        return $this->media_type;
    }

    public function getParams() {
        return $this->params;
    }

    /** the q parameter can either be set or defaults to 1 */
    public function getQualityFactor() {
        return isset($this->params['q']) ? (float) $this->params['q'] : 1;
    }

    public function __toString() {
        $mapped_params = [$this->media_type];

        foreach ($this->params as $k => $v) {
            $mapped_params[] = $k . '=' . $v;
        }

        return implode(';', $mapped_params);
    }

    /** compare this to another accept header item and return -1, 0, or 1 depending
        their comparison */
    public function cmp($b) {
        if ($this->getQualityFactor() != $b->getQualityFactor()) {
            return $this->getQualityFactor() > $b->getQualityFactor() ? 1 : -1;
        }

        if ($this->getMediaType() == $b->getMediaType()) {
            return count($this->getParams()) - count($b->getParams());
        }

        return strlen($this->getMediaType()) - strlen($b->getMediaType());
    }
}
