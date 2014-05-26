<?php

namespace FFMpeg\Filters\HLS;

use FFMpeg\Media\HLS;
use FFMpeg\Format\HLSInterface;

class SimpleFilter implements HLSFilterInterface
{
    private $params;
    private $priority;

    public function __construct(array $params, $priority = 0)
    {
        $this->params = $params;
        $this->priority = $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(HLS $media, HLSInterface $format)
    {
        return $this->params;
    }
}
